<?php

namespace App\Notifications\Messages;

class GoogleChatMessage
{
    private string $headerTitle = '';
    private string $headerSubtitle = '';
    private ?string $headerImageUrl = null;
    private string $text = '';
    private ?string $icon = null;
    private ?string $buttonText = null;
    private ?string $buttonUrl = null;
    private ?array $buttonColor = null;

    public static function create(): self
    {
        return new self();
    }

    public function header(string $title, string $subtitle = ''): self
    {
        $this->headerTitle = $title;
        $this->headerSubtitle = $subtitle;

        return $this;
    }

    public function headerImage(string $url): self
    {
        $this->headerImageUrl = $url;

        return $this;
    }

    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function icon(string $knownIcon): self
    {
        $this->icon = $knownIcon;

        return $this;
    }

    public function button(string $text, string $url, ?array $color = null): self
    {
        $this->buttonText = $text;
        $this->buttonUrl = $url;
        $this->buttonColor = $color;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'cardsV2' => [
                [
                    'cardId' => 'sendwich-card',
                    'card' => [
                        'header' => $this->buildHeader(),
                        'sections' => [
                            ['widgets' => $this->buildWidgets()],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function buildHeader(): array
    {
        $header = ['title' => $this->headerTitle ?: 'Sendwich'];

        if ($this->headerSubtitle) {
            $header['subtitle'] = $this->headerSubtitle;
        }

        if ($this->headerImageUrl) {
            $header['imageUrl'] = $this->headerImageUrl;
            $header['imageType'] = 'CIRCLE';
        }

        return $header;
    }

    private function buildWidgets(): array
    {
        $widgets = [];

        if ($this->text) {
            $decoratedText = ['text' => $this->text];

            if ($this->icon) {
                $decoratedText['startIcon'] = ['knownIcon' => $this->icon];
            }

            $widgets[] = ['decoratedText' => $decoratedText];
        }

        if ($this->buttonText && $this->buttonUrl) {
            $button = [
                'text' => $this->buttonText,
                'onClick' => [
                    'openLink' => ['url' => $this->buttonUrl],
                ],
            ];

            if ($this->buttonColor) {
                $button['color'] = $this->buttonColor;
            }

            $widgets[] = [
                'buttonList' => [
                    'buttons' => [$button],
                ],
            ];
        }

        return $widgets;
    }
}
