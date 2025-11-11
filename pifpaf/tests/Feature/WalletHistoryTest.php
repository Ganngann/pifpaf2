<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use App\Models\BankAccount;
use App\Models\WalletHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletHistoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste l'affichage de l'historique du portefeuille.
     *
     * @return void
     */
    public function test_wallet_history_is_displayed_on_wallet_page()
    {
        $user = User::factory()->create(['wallet' => 100]);
        WalletHistory::factory()->create(['user_id' => $user->id, 'type' => 'credit', 'amount' => 50, 'description' => 'Vente item 1']);
        WalletHistory::factory()->create(['user_id' => $user->id, 'type' => 'debit', 'amount' => 20, 'description' => 'Achat item 2']);

        $response = $this->actingAs($user)->get(route('wallet.show'));

        $response->assertStatus(200);
        $response->assertSee('Solde actuel');
        $response->assertSee('100,00 €');
        $response->assertSee('Historique des opérations');
        $response->assertSee('Vente item 1');
        $response->assertSee('+50,00 €');
        $response->assertSee('Achat item 2');
        $response->assertSee('-20,00 €');
    }

    /**
     * Teste la création de l'historique du portefeuille après la confirmation de la réception.
     *
     * @return void
     */
    public function test_wallet_history_is_created_after_reception_confirmation()
    {
        $seller = User::factory()->create(['wallet' => 0]);
        $buyer = User::factory()->create(['wallet' => 50]);
        $item = Item::factory()->create(['user_id' => $seller->id, 'title' => 'Mon Super Article']);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'amount' => 30]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'amount' => 30, 'status' => 'payment_received']);

        $this->actingAs($buyer)->patch(route('transactions.confirm-reception', $transaction));

        $this->assertDatabaseHas('wallet_histories', [
            'user_id' => $seller->id,
            'type' => 'credit',
            'amount' => 30,
            'description' => 'Vente de l\'article : Mon Super Article',
        ]);

    }

    /**
     * Teste la demande de virement.
     *
     * @return void
     */
    public function test_user_can_request_a_withdrawal()
    {
        $user = User::factory()->create(['wallet' => 100]);
        $bankAccount = BankAccount::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('wallet.withdraw'), [
            'amount' => 50,
            'bank_account_id' => $bankAccount->id,
        ]);

        $response->assertRedirect(route('wallet.show'));
        $response->assertSessionHas('success', 'Votre demande de virement a bien été enregistrée.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'wallet' => 50, // Wallet is reduced immediately to "freeze" funds
        ]);

        $this->assertDatabaseHas('withdrawal_requests', [
            'user_id' => $user->id,
            'bank_account_id' => $bankAccount->id,
            'amount' => 50,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('wallet_histories', [
            'user_id' => $user->id,
            'type' => 'debit',
            'amount' => 50,
        ]);
    }
}
