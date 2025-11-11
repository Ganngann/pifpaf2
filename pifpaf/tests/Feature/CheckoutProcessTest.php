<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Address;
use App\Models\Transaction;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CheckoutProcessTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function shipping_address_is_saved_with_the_transaction_during_checkout(): void
    {
        // 1. Arrange
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $shippingAddress = Address::factory()->for($buyer)->create(['is_for_delivery' => true]);
        $item = Item::factory()->for($seller)->create(['delivery_available' => true]);
        $offer = Offer::factory()->for($item)->for($buyer)->create(['status' => 'accepted', 'delivery_method' => 'delivery']);

        // Fake the session. This is what PaymentController@create would do.
        session()->put('payment_address_id', $shippingAddress->id);

        // Mock the static call to the Stripe API
        $intent = new \stdClass();
        $intent->status = 'succeeded';
        $intent->amount = (int) round($offer->amount * 100);

        $mock = \Mockery::mock('alias:Stripe\PaymentIntent');
        $mock->shouldReceive('retrieve')
            ->with('pi_mock_success')
            ->andReturn($intent);

        // 2. Act
        $response = $this->actingAs($buyer)->post(route('payment.store', $offer), [
            'use_wallet' => false,
            'payment_intent_id' => 'pi_mock_success',
        ]);

        // 3. Assert
        $response->assertRedirect();
        $transaction = Transaction::where('offer_id', $offer->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals($shippingAddress->id, $transaction->address_id);
    }
}
