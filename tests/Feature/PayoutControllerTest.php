<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Order;
use App\Models\Settlement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PayoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_payout_creates_a_settlement_and_no_order(): void
    {
        Mail::fake();

        $company = Company::forceCreate(['name' => 'Corp', 'link' => 'corp', 'token' => 'token']);
        $runner = User::forceCreate(['name' => 'R', 'email' => 'r@c', 'password' => bcrypt('x'), 'company_id' => $company->id]);
        $orderer = User::forceCreate(['name' => 'O', 'email' => 'o@c', 'password' => bcrypt('x'), 'company_id' => $company->id]);

        // The runner fronted 10 for the orderer, so the runner is owed 10 (positive balance).
        Order::forceCreate([
            'user_id'    => $orderer->id,
            'company_id' => $company->id,
            'product_id' => null,
            'quantity'   => 1,
            'paid_by'    => $runner->id,
            'total'      => 10.0,
            'date'       => Carbon::now()->setTime(12, 15),
        ]);

        $response = $this->actingAs($runner)
            ->postJson('/api/payouts/handle', [
                'payouts' => [['id' => $orderer->id, 'paysBack' => 10.0]],
            ]);

        $response->assertOk();

        $this->assertSame(1, Settlement::query()->count());
        $settlement = Settlement::query()->first();
        $this->assertSame($orderer->id, $settlement->payer_id);
        $this->assertSame($runner->id, $settlement->receiver_id);
        $this->assertEqualsWithDelta(10.0, $settlement->amount, 0.001);

        $this->assertSame(0, Order::query()->where('product_id', '=', Order::PAYOUT_PRODUCT_ID)->count(), 'Payouts must no longer create orders.');
    }
}