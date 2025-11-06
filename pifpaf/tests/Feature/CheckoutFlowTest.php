<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
