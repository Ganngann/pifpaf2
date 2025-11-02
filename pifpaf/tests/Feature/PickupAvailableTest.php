<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\PickupAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PickupAvailableTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function pickup_available_option_is_saved_when_creating_an_item(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $pickupAddress = PickupAddress::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $itemData = [
            'title' => 'Mon article',
            'description' => 'Une super description',
            'category' => 'Vêtements',
            'price' => 10,
            'images' => [UploadedFile::fake()->image('photo.jpg')],
            'pickup_available' => true,
            'pickup_address_id' => $pickupAddress->id,
        ];

        $this->post(route('items.store'), $itemData);

        $this->assertDatabaseHas('items', [
            'title' => 'Mon article',
            'pickup_available' => true,
            'pickup_address_id' => $pickupAddress->id,
        ]);
    }

    #[Test]
    public function pickup_code_is_generated_for_pickup_available_item_transaction(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'pickup_available' => true]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'status' => 'accepted']);

        $this->actingAs($buyer);

        $response = $this->post(route('payment.store', $offer));

        $response->assertRedirect(route('dashboard'));
        $transaction = Transaction::where('offer_id', $offer->id)->first();

        $this->assertNotNull($transaction->pickup_code);
        $this->assertEquals(6, strlen($transaction->pickup_code));
    }

    #[Test]
    public function pickup_code_is_not_generated_for_non_pickup_item_transaction(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'pickup_available' => false]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'status' => 'accepted']);

        $this->actingAs($buyer);

        $this->post(route('payment.store', $offer));
        $transaction = Transaction::where('offer_id', $offer->id)->first();

        $this->assertNull($transaction->pickup_code);
    }

    #[Test]
    public function buyer_can_see_pickup_code_on_dashboard(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'pickup_available' => true]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'status' => 'accepted']);
        $this->actingAs($buyer)->post(route('payment.store', $offer));
        $transaction = $offer->refresh()->transaction;

        $response = $this->actingAs($buyer)->get(route('dashboard'));

        $response->assertSee('Code de retrait :');
        $response->assertSee($transaction->pickup_code);
    }

    #[Test]
    public function seller_can_see_pickup_section_on_dashboard(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'pickup_available' => true]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'status' => 'accepted']);

        // Simuler le paiement
        $this->actingAs($buyer)->post(route('payment.store', $offer));
        $transaction = $offer->refresh()->transaction;

        // Mettre à jour manuellement les statuts comme le feraient les écouteurs d'événements
        $item->update(['status' => 'sold']);
        $offer->update(['status' => 'paid']);

        $response = $this->actingAs($seller)->get(route('dashboard'));

        $response->assertSee('Ventes à retirer');
        $response->assertSee('Acheteur : ' . $buyer->name);
        $response->assertSee($transaction->pickup_code);
    }

    #[Test]
    public function seller_can_confirm_pickup(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'pickup_available' => true]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'status' => 'accepted']);
        $this->actingAs($buyer)->post(route('payment.store', $offer));
        $transaction = $offer->refresh()->transaction;

        $response = $this->actingAs($seller)->patch(route('transactions.confirm-pickup', $transaction));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'pickup_completed',
        ]);
    }

    #[Test]
    public function pickup_section_disappears_after_confirmation(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'pickup_available' => true]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'status' => 'accepted']);

        // Simuler le paiement
        $this->actingAs($buyer)->post(route('payment.store', $offer));
        $transaction = $offer->refresh()->transaction;

        // Confirmer le retrait
        $this->actingAs($seller)->patch(route('transactions.confirm-pickup', $transaction));

        $response = $this->actingAs($seller)->get(route('dashboard'));

        $response->assertDontSee('Ventes à retirer');
    }
}
