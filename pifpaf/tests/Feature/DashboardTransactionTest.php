<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use PHPUnit\Framework\Attributes\Test;

class DashboardTransactionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function open_sales_are_displayed_on_dashboard_for_seller(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status' => 'accepted',
        ]);
        Transaction::factory()->create([
            'offer_id' => $offer->id,
            'status' => 'payment_received',
        ]);

        $response = $this->actingAs($seller)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Transactions en cours');
        $response->assertSee($item->title);
        $response->assertSeeText('Acheteur : ' . $buyer->name);
    }

    #[Test]
    public function open_purchases_are_displayed_on_dashboard_for_buyer(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status' => 'accepted',
        ]);
        Transaction::factory()->create([
            'offer_id' => $offer->id,
            'status' => 'payment_received',
        ]);

        $response = $this->actingAs($buyer)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Transactions en cours');
        $response->assertSee($item->title);
        $response->assertSeeText('Vendeur : ' . $seller->name);
    }

    #[Test]
    public function completed_transactions_are_not_displayed_in_open_transactions(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'status' => 'paid',
        ]);
        Transaction::factory()->create([
            'offer_id' => $offer->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($buyer)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee($item->title);
        $response->assertSee('Vous n\'avez aucune transaction en cours.', false);
    }
}
