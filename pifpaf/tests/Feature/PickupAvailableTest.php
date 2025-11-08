<?php

namespace Tests\Feature;

use App\Enums\AddressType;
use App\Models\Address;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Mockery;
use Stripe\PaymentIntent;

class PickupAvailableTest extends TestCase
{
    use RefreshDatabase;

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function mockStripePaymentIntent(string $id, float $amount)
    {
        Mockery::mock('alias:' . PaymentIntent::class)
            ->shouldReceive('retrieve')
            ->once()
            ->with($id)
            ->andReturn((object)[
                'id' => $id,
                'status' => 'succeeded',
                'amount' => (int) round($amount * 100),
            ]);
    }

    #[Test]
    public function pickup_available_option_is_saved_when_creating_an_item(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id, 'type' => AddressType::PICKUP]);
        $this->actingAs($user);

        $itemData = [
            'title' => 'Mon article',
            'description' => 'Une super description',
            'category' => 'Vêtements',
            'price' => 10,
            'images' => [UploadedFile::fake()->image('photo.jpg')],
            'pickup_available' => true,
            'address_id' => $address->id,
        ];

        $this->post(route('items.store'), $itemData);

        $this->assertDatabaseHas('items', [
            'title' => 'Mon article',
            'pickup_available' => true,
            'address_id' => $address->id,
        ]);
    }

    #[Test]
    public function pickup_code_is_generated_for_pickup_available_item_transaction(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'pickup_available' => true]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'status' => 'accepted', 'amount' => 10.00]);
        $this->mockStripePaymentIntent('pi_fake', $offer->amount);

        $this->actingAs($buyer);

        $response = $this->post(route('payment.store', $offer), [
            'payment_intent_id' => 'pi_fake',
            'use_wallet' => false,
        ]);

        $transaction = Transaction::where('offer_id', $offer->id)->first();
        $response->assertRedirect(route('checkout.success', $transaction));

        $this->assertNotNull($transaction->pickup_code);
        $this->assertEquals(6, strlen($transaction->pickup_code));
    }

    #[Test]
    public function pickup_code_is_not_generated_for_non_pickup_item_transaction(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'pickup_available' => false]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'status' => 'accepted', 'amount' => 10.00]);
        $this->mockStripePaymentIntent('pi_fake', $offer->amount);

        $this->actingAs($buyer);

        $this->post(route('payment.store', $offer), ['payment_intent_id' => 'pi_fake', 'use_wallet' => false]);
        $transaction = Transaction::where('offer_id', $offer->id)->first();

        $this->assertNull($transaction->pickup_code);
    }

    #[Test]
    public function buyer_can_see_pickup_code_on_dashboard(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'pickup_available' => true]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'status' => 'accepted',
            'amount' => 10.00,
            'delivery_method' => 'pickup'
        ]);
        $this->mockStripePaymentIntent('pi_fake', $offer->amount);
        $this->actingAs($buyer)->post(route('payment.store', $offer), ['payment_intent_id' => 'pi_fake']);
        $transaction = $offer->refresh()->transaction;

        $response = $this->actingAs($buyer)->get(route('dashboard'));

        $response->assertSee('Transactions en cours');
        $response->assertSee($transaction->pickup_code);
    }

    #[Test]
    public function seller_can_see_pickup_section_on_dashboard(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'pickup_available' => true]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'status' => 'accepted', 'amount' => 10.00]);
        $this->mockStripePaymentIntent('pi_fake', $offer->amount);

        // Simuler le paiement
        $this->actingAs($buyer)->post(route('payment.store', $offer), ['payment_intent_id' => 'pi_fake']);
        $transaction = $offer->refresh()->transaction;

        $response = $this->actingAs($seller)->get(route('dashboard'));

        $response->assertSee('Transactions en cours');
        $response->assertSee(e('Acheteur : ' . $buyer->name), false);
        $response->assertSee($transaction->pickup_code);
    }

    #[Test]
    public function seller_can_confirm_pickup(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id, 'pickup_available' => true]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'status' => 'accepted', 'amount' => 10.00]);
        $this->mockStripePaymentIntent('pi_fake', $offer->amount);
        $this->actingAs($buyer)->post(route('payment.store', $offer), ['payment_intent_id' => 'pi_fake']);
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
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'status' => 'accepted', 'amount' => 10.00]);
        $this->mockStripePaymentIntent('pi_fake', $offer->amount);

        // Simuler le paiement
        $this->actingAs($buyer)->post(route('payment.store', $offer), ['payment_intent_id' => 'pi_fake']);
        $transaction = $offer->refresh()->transaction;

        // Confirmer le retrait
        $this->actingAs($seller)->patch(route('transactions.confirm-pickup', $transaction));

        $response = $this->actingAs($seller)->get(route('dashboard'));

        $response->assertViewHas('openTransactions', function ($transactions) use ($transaction) {
            return !$transactions->contains($transaction);
        });
    }
}
