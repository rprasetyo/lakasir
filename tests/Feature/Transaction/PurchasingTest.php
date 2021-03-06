<?php

namespace Tests\Feature\Transaction;

use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Price;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchasingTest extends TestCase
{
    public function test_success_create_purchasing(): void
    {
        $user = User::find(1);
        $response = $this->actingAs($user)->post('/transaction/purchasing', $this->data());
        /* dump($item->log_stocks->last(), $item2->log_stocks->last()); */

        $response->assertStatus(302);
        $response->assertRedirect('/transaction/purchasing');
    }

    protected function data()
    {
        factory(Supplier::class, 5)->create();
        factory(Price::class, 10)->create();
        $paymentMethod = PaymentMethod::inRandomOrder()->where('visible_in->purchasing', true)->first();
        $items = Item::where('internal_production', false)->inRandomOrder()->limit(2)->get();
        $item2 = $items->last();
        $item = $items->first();
        /* dump($item->log_stocks->last(), $item2->log_stocks->last()); */
        $supplier = Supplier::inRandomOrder()->limit(1)->first();
        return [
            'supplier_id' => $supplier->id,
            'payment_method' => $paymentMethod->id,
            'items' => [
                [
                    'item_id' => $item->id,
                    'initial_price' => $item->prices->last()->initial_price,
                    'selling_price' => $item->prices->last()->selling_price,
                    'qty' => 20
                ],
                [
                    'item_id' => $item2->id,
                    'initial_price' => $item2->prices->last()->initial_price,
                    'selling_price' => $item2->prices->last()->selling_price,
                    'qty' => 20
                ]
            ]
        ];
    }
}
