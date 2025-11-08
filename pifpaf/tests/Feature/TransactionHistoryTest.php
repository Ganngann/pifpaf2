<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_history_pages()
    {
        $this->get(route('transactions.purchases'))->assertRedirect('login');
        $this->get(route('transactions.sales'))->assertRedirect('login');
    }

    public function test_user_can_see_their_purchases()
    {
        $user = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['user_id' => $user->id, 'item_id' => $item->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id]);

        $this->actingAs($user)
            ->get(route('transactions.purchases'))
            ->assertSuccessful()
            ->assertSee($item->title)
            ->assertSee($seller->name)
            ->assertSee(route('profile.show', $seller));
    }

    public function test_user_cannot_see_other_users_purchases()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $item = Item::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $otherUser->id, 'item_id' => $item->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id]);

        $this->actingAs($user)
            ->get(route('transactions.purchases'))
            ->assertSuccessful()
            ->assertDontSee($item->title);
    }

    public function test_user_can_see_their_sales()
    {
        $user = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $buyer->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id]);

        $this->actingAs($user)
            ->get(route('transactions.sales'))
            ->assertSuccessful()
            ->assertSee($item->title)
            ->assertSee($buyer->name)
            ->assertSee(route('profile.show', $buyer));
    }

    public function test_user_cannot_see_other_users_sales()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $otherUser->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id]);
        $transaction = Transaction::factory()->create(['offer_id' => $offer->id]);

        $this->actingAs($user)
            ->get(route('transactions.sales'))
            ->assertSuccessful()
            ->assertDontSee($item->title);
    }
}
