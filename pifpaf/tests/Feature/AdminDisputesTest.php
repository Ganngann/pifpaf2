<?php

namespace Tests\Feature;

use App\Models\Dispute;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDisputesTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $regularUser;
    private Dispute $dispute;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);

        // Create a full transaction context for the dispute
        $seller = User::factory()->create();
        $buyer = $this->regularUser;
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id]);
        $this->dispute = Dispute::factory()->create(['transaction_id' => $transaction->id, 'user_id' => $buyer->id]);
    }

    /** @test */
    public function guests_are_redirected_to_login()
    {
        $this->get(route('admin.disputes.index'))->assertRedirect('login');
        $this->get(route('admin.disputes.show', $this->dispute))->assertRedirect('login');
    }

    /** @test */
    public function non_admin_users_are_forbidden()
    {
        $this->actingAs($this->regularUser);

        $this->get(route('admin.disputes.index'))->assertForbidden();
        $this->get(route('admin.disputes.show', $this->dispute))->assertForbidden();
    }

    /** @test */
    public function admin_can_view_disputes_index_page()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.disputes.index'));

        $response->assertOk();
        $response->assertViewIs('admin.disputes.index');
        $response->assertSeeText('Gestion des Litiges');
        $response->assertSeeText('Transaction ID');
        $response->assertSeeText($this->dispute->transaction_id);
    }

    /** @test */
    public function admin_can_view_dispute_show_page()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('admin.disputes.show', $this->dispute));

        $response->assertOk();
        $response->assertViewIs('admin.disputes.show');
        $response->assertSeeText('Détail du Litige');
        $response->assertSeeText($this->dispute->reason);
        $response->assertSeeText($this->dispute->user->name); // Complainant
        $response->assertSeeText($this->dispute->transaction->offer->user->name); // Buyer
        $response->assertSeeText($this->dispute->transaction->offer->item->user->name); // Seller
    }

    /** @test */
    public function admin_can_resolve_dispute_for_buyer()
    {
        $this->actingAs($this->adminUser);

        $buyer = $this->dispute->transaction->offer->user;
        $initialBuyerWallet = $buyer->wallet;
        $transactionAmount = $this->dispute->transaction->amount;

        $response = $this->post(route('admin.disputes.resolveForBuyer', $this->dispute));

        $response->assertRedirect(route('admin.disputes.index'));
        $this->assertDatabaseHas('disputes', ['id' => $this->dispute->id, 'status' => 'closed']);
        $this->assertDatabaseHas('transactions', ['id' => $this->dispute->transaction_id, 'status' => 'refunded']);
        $this->assertEquals($initialBuyerWallet + $transactionAmount, $buyer->fresh()->wallet);
    }

    /** @test */
    public function admin_can_resolve_dispute_for_seller()
    {
        $this->actingAs($this->adminUser);

        $seller = $this->dispute->transaction->offer->item->user;
        $initialSellerWallet = $seller->wallet;
        $transactionAmount = $this->dispute->transaction->amount;

        $response = $this->post(route('admin.disputes.resolveForSeller', $this->dispute));

        $response->assertRedirect(route('admin.disputes.index'));
        $this->assertDatabaseHas('disputes', ['id' => $this->dispute->id, 'status' => 'closed']);
        $this->assertDatabaseHas('transactions', ['id' => $this->dispute->transaction_id, 'status' => 'completed']);
        $this->assertEquals($initialSellerWallet + $transactionAmount, $seller->fresh()->wallet);
    }

    /** @test */
    public function admin_can_close_dispute()
    {
        $this->actingAs($this->adminUser);

        $response = $this->post(route('admin.disputes.close', $this->dispute));

        $response->assertRedirect(route('admin.disputes.index'));
        $this->assertDatabaseHas('disputes', ['id' => $this->dispute->id, 'status' => 'closed']);
    }
}
