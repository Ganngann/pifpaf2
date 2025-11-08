<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a user is redirected to the order summary page after a buy now action.
     *
     * @return void
     */
    public function test_buy_now_redirects_to_summary_page()
    {
        // Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 100]);

        // Act
        $response = $this->actingAs($buyer)->post(route('offers.buyNow', $item), [
            'delivery_method' => 'pickup'
        ]);

        // Assert
        $response->assertStatus(302);
        $response->assertRedirectContains('/checkout/');
        $response->assertRedirectContains('/summary');
    }

    /**
     * Test that a user is redirected to the success page after a successful payment.
     *
     * @return void
     */
    public function test_successful_payment_redirects_to_success_page()
    {
        // Arrange
        // We mock the Stripe API static call to simulate a successful payment
        $intentMock = Mockery::mock('alias:\Stripe\PaymentIntent');
        $intentMock->shouldReceive('retrieve')
            ->once()
            ->with('pi_mock_id')
            ->andReturn((object)[
                'id' => 'pi_mock_id',
                'status' => 'succeeded',
                'amount' => 100 * 100, // 100 EUR in cents
                'currency' => 'eur',
            ]);

        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 100]);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'amount' => 100,
            'status' => 'accepted'
        ]);

        // Act
        $response = $this->actingAs($buyer)->post(route('payment.store', $offer), [
            'payment_intent_id' => 'pi_mock_id',
            'use_wallet' => false
        ]);

        // Assert
        $this->assertDatabaseHas('transactions', [
            'offer_id' => $offer->id,
            'status' => 'payment_received'
        ]);

        $transaction = $offer->transaction()->first();
        $response->assertStatus(302);
        $response->assertRedirect(route('checkout.success', $transaction));

        // Follow the redirect to the success page
        $successResponse = $this->get($response->headers->get('Location'));
        $successResponse->assertStatus(200);
        $successResponse->assertSee('Paiement effectué avec succès !');
        $successResponse->assertSee($item->title);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
