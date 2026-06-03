<?php

namespace App\Actions;

use App\Exceptions\ReceiptAnalysisException;
use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class AnalyzeReceipt
{
    private const ENDPOINT = 'https://api.anthropic.com/v1/messages';
    private const TOOL_NAME = 'report_prices';

    public function __construct(
        private string $imageContents,
        private string $mediaType,
    ) {
    }

    /**
     * Send the receipt image to Claude, match each order to a price, and identify the store.
     *
     * @param Collection<int, Order> $orders
     * @param Collection<int, \App\Models\Store> $stores  Company stores to match the receipt against.
     * @return array{prices: array<int, float|null>, extras: array<int, array{label: string, price: float}>, store: array{store_id: int|null, store_name: string|null}}
     *         'prices'  => map of order id => extracted price (null when no match was found);
     *         'extras'  => receipt lines that did not match any order;
     *         'store'   => the store the receipt is from (matched to a known store when possible).
     * @throws ReceiptAnalysisException
     */
    public function execute(Collection $orders, Collection $stores): array
    {
        $apiKey = config('services.anthropic.api_key');
        if (!$apiKey) {
            throw ReceiptAnalysisException::missingApiKey();
        }

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'x-api-key'         => $apiKey,
                    'anthropic-version' => config('services.anthropic.version'),
                    'content-type'      => 'application/json',
                ])
                ->post(self::ENDPOINT, $this->payload($orders, $stores));
        } catch (ConnectionException $exception) {
            Log::error('Anthropic receipt analysis connection failed', ['message' => $exception->getMessage()]);
            throw ReceiptAnalysisException::requestFailed(0, $exception->getMessage());
        }

        if ($response->failed()) {
            Log::error('Anthropic receipt analysis failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw ReceiptAnalysisException::requestFailed($response->status(), $response->body());
        }

        return $this->extractResult($response->json());
    }

    private function payload(Collection $orders, Collection $stores): array
    {
        return [
            'model'      => config('services.anthropic.model'),
            'max_tokens' => 2048,
            'system'     => [
                [
                    'type'          => 'text',
                    'text'          => 'You read prices from a photo of a store receipt. You are given the list of ordered items. '
                        . 'For every order, find the matching line on the receipt and report the price actually paid for that line '
                        . '(the total for that line, including its quantity). If an order is not on the receipt, report null for it. '
                        . 'Any line on the receipt that does NOT correspond to one of the given orders is an extra item that was '
                        . 'added during pickup — report each of those in extra_items with a short (singular) description, its quantity, '
                        . 'and the price of a SINGLE unit. If a line covers multiple units (e.g. "2x Broodje"), set quantity to that '
                        . 'number and price to the per-unit price (divide the line total by the quantity when only a total is shown). '
                        . 'Do not put ordered items in extra_items, and do not invent items that are not on the receipt. '
                        . 'You are also given the list of known stores. Identify which store the receipt is from using the '
                        . 'store/merchant name printed on it: set store.store_id to the id of the matching known store, or null '
                        . 'if none matches; set store.store_name to the store name exactly as printed on the receipt. '
                        . 'Always call the report_prices tool exactly once.',
                    'cache_control' => ['type' => 'ephemeral'],
                ],
            ],
            'tools'       => [$this->tool()],
            'tool_choice' => ['type' => 'tool', 'name' => self::TOOL_NAME],
            'messages'    => [
                [
                    'role'    => 'user',
                    'content' => [
                        [
                            'type'   => 'image',
                            'source' => [
                                'type'       => 'base64',
                                'media_type' => $this->mediaType,
                                'data'       => base64_encode($this->imageContents),
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => "Here is the context as JSON — the ordered items and the known stores. "
                                . "Match each order to its price, list extra items, and identify which known store this receipt is from.\n\n"
                                . json_encode([
                                    'orders'       => $this->orderContext($orders),
                                    'known_stores' => $this->storeContext($stores),
                                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                        ],
                    ],
                ],
            ],
        ];
    }

    private function tool(): array
    {
        return [
            'name'        => self::TOOL_NAME,
            'description' => 'Report the price read from the receipt for each ordered item.',
            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'prices' => [
                        'type'        => 'array',
                        'description' => 'One entry per given order id.',
                        'items' => [
                            'type'       => 'object',
                            'properties' => [
                                'order_id' => ['type' => 'integer', 'description' => 'The id of the order this price belongs to.'],
                                'price'    => ['type' => ['number', 'null'], 'description' => 'Price paid for this line, or null if not on the receipt.'],
                            ],
                            'required' => ['order_id', 'price'],
                        ],
                    ],
                    'extra_items' => [
                        'type'        => 'array',
                        'description' => 'Receipt lines that do not match any given order (added during pickup).',
                        'items' => [
                            'type'       => 'object',
                            'properties' => [
                                'description' => ['type' => 'string', 'description' => 'Short singular name of the extra item as printed on the receipt.'],
                                'quantity'    => ['type' => 'integer', 'description' => 'How many units of this item are on this line (1 or more).'],
                                'price'       => ['type' => 'number', 'description' => 'Price of a SINGLE unit of this item (line total divided by quantity when only a total is shown).'],
                            ],
                            'required' => ['description', 'quantity', 'price'],
                        ],
                    ],
                    'store' => [
                        'type'        => 'object',
                        'description' => 'The store this receipt is from.',
                        'properties'  => [
                            'store_id'   => ['type' => ['integer', 'null'], 'description' => 'Id of the matching known store, or null if none matches.'],
                            'store_name' => ['type' => ['string', 'null'], 'description' => 'Store/merchant name as printed on the receipt.'],
                        ],
                        'required' => ['store_id', 'store_name'],
                    ],
                ],
                'required' => ['prices', 'extra_items', 'store'],
            ],
        ];
    }

    /**
     * @param Collection<int, Order> $orders
     */
    private function orderContext(Collection $orders): array
    {
        return $orders->map(fn (Order $order) => [
            'order_id' => $order->id,
            'product'  => $order->product?->name ?? $order->label,
            'quantity' => $order->quantity,
            'comment'  => $order->comment,
        ])->values()->all();
    }

    /**
     * @param Collection<int, \App\Models\Store> $stores
     */
    private function storeContext(Collection $stores): array
    {
        return $stores->map(fn ($store) => [
            'id'   => $store->id,
            'name' => $store->name,
        ])->values()->all();
    }

    /**
     * @return array{prices: array<int, float|null>, extras: array<int, array{label: string, price: float}>, store: array{store_id: int|null, store_name: string|null}}
     * @throws ReceiptAnalysisException
     */
    private function extractResult(array $body): array
    {
        $toolUse = collect($body['content'] ?? [])
            ->first(fn ($block) => ($block['type'] ?? null) === 'tool_use' && ($block['name'] ?? null) === self::TOOL_NAME);

        if (!$toolUse) {
            throw ReceiptAnalysisException::noResult();
        }

        $prices = [];
        foreach ($toolUse['input']['prices'] ?? [] as $entry) {
            if (!isset($entry['order_id'])) {
                continue;
            }
            $price = $entry['price'] ?? null;
            $prices[(int) $entry['order_id']] = $price === null ? null : (float) $price;
        }

        $extras = [];
        foreach ($toolUse['input']['extra_items'] ?? [] as $entry) {
            if (!isset($entry['description'], $entry['price'])) {
                continue;
            }
            // A single receipt line with quantity N becomes N separate items so each can be assigned individually.
            $quantity = max(1, min((int) ($entry['quantity'] ?? 1), 50));
            for ($unit = 0; $unit < $quantity; $unit++) {
                $extras[] = [
                    'label' => (string) $entry['description'],
                    'price' => (float) $entry['price'],
                ];
            }
        }

        $storeInput = $toolUse['input']['store'] ?? [];
        $storeId = $storeInput['store_id'] ?? null;
        $store = [
            'store_id'   => $storeId === null ? null : (int) $storeId,
            'store_name' => $storeInput['store_name'] ?? null,
        ];

        return ['prices' => $prices, 'extras' => $extras, 'store' => $store];
    }
}