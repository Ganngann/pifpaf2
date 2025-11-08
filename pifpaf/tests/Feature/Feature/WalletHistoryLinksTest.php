<?php

namespace Tests\Feature\Feature;

use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletHistoryLinksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_a_link_to_the_transaction_for_the_seller_on_credit_history()
    {
        // 1. Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id]);

        // Manually create the wallet history entry for the seller's credit
        WalletHistory::create([
            'user_id' => $seller->id,
            'type' => 'credit',
            'amount' => $transaction->amount,
            'description' => 'Vente de l\'article : ' . $item->title,
            'transaction_id' => $transaction->id,
        ]);

        // 2. Act
        $response = $this->actingAs($seller)->get(route('wallet.show'));

        // 3. Assert
        $response->assertStatus(200);
        $response->assertSee('Vente de l\'article : ' . $item->title);

        // Assert that the description is inside a link pointing to the correct transaction
        $expectedLink = route('transactions.show', $transaction);
        $this->assertMatchesRegularExpression('/<a href="' . preg_quote($expectedLink, '/') . '"/', $response->getContent());
    }

    /** @test */
    public function it_displays_a_link_to_the_transaction_for_the_buyer_on_debit_history()
    {
        // 1. Arrange
        $seller = User::factory()->create();
        $buyer = User::factory()->create(['wallet' => 100]); // Buyer needs money
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id, 'amount' => 50]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'amount' => 50]);

        // Manually create the wallet history entry for the buyer's debit
        WalletHistory::create([
            'user_id' => $buyer->id,
            'type' => 'debit',
            'amount' => $transaction->amount,
            'description' => 'Achat de l\'article : ' . $item->title,
            'transaction_id' => $transaction->id,
        ]);

        // 2. Act
        $response = $this->actingAs($buyer)->get(route('wallet.show'));

        // 3. Assert
        $response->assertStatus(200);
        $response->assertSee('Achat de l\'article : ' . $item->title);

        // Assert that the description is inside a link pointing to the correct transaction
        $expectedLink = route('transactions.show', $transaction);
        $this->assertMatchesRegularExpression('/<a href="' . preg_quote($expectedLink, '/') . '"/', $response->getContent());
    }

    /** @test */
    public function it_does_not_display_a_link_for_history_without_a_transaction()
    {
        // 1. Arrange
        $user = User::factory()->create();

        // Create a wallet history entry for a withdrawal (no transaction)
        WalletHistory::create([
            'user_id' => $user->id,
            'type' => 'withdrawal',
            'amount' => 50,
            'description' => 'Retrait de fonds',
            'transaction_id' => null,
        ]);

        // 2. Act
        $response = $this->actingAs($user)->get(route('wallet.show'));

        // 3. Assert
        $response->assertStatus(200);
        $response->assertSee('Retrait de fonds');

        // Assert that the description is NOT inside a link
        $this->assertDoesNotMatchRegularExpression('/<a href=".*">Retrait de fonds<\/a>/', $response->getContent());
    }
}
