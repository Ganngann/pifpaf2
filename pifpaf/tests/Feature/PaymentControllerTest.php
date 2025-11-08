<?php

namespace Tests\Feature;

use App\Enums\ItemStatus;
use App\Models\Item;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Stripe\PaymentIntent;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class PaymentControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function an_unauthorized_user_cannot_access_the_payment_page(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $unauthorizedUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($unauthorizedUser)->get(route('payment.create', $offer));

        $response->assertStatus(403);
    }

    #[Test]
    public function the_payment_page_cannot_be_displayed_for_a_non_accepted_offer(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($buyer)->get(route('payment.create', $offer));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasErrors('payment');
    }

    #[Test]
    public function an_authorized_user_can_view_the_payment_page_with_intent(): void
    {
        Mockery::mock('alias:' . PaymentIntent::class)
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'id' => 'pi_123',
                'client_secret' => 'pi_123_secret_456',
            ]);

        $seller = User::factory()->create();
        $buyer = User::factory()->create(['wallet' => 5.00]);
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'amount' => 20.00,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($buyer)->get(route('payment.create', $offer));

        $response->assertStatus(200);
        $response->assertViewIs('payment.create');
        $response->assertViewHasAll(['offer', 'walletBalance', 'intent']);
        $this->assertNotNull($response->viewData('intent'));
    }

    #[Test]
    public function a_payment_intent_is_not_created_if_amount_is_too_low(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'amount' => 0.40,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($buyer)->get(route('payment.create', $offer));

        $response->assertStatus(200);
        $response->assertViewIs('payment.create');
        $response->assertViewHas('intent', null);
    }

    #[Test]
    public function processes_a_successful_payment_by_card_only(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create(['wallet' => 0]);
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'amount' => 50.00,
            'status' => 'accepted',
        ]);

        Mockery::mock('alias:' . PaymentIntent::class)
            ->shouldReceive('retrieve')
            ->once()
            ->with('pi_123')
            ->andReturn((object)[
                'id' => 'pi_123',
                'status' => 'succeeded',
                'amount' => 5000,
            ]);

        $response = $this->actingAs($buyer)->post(route('payment.store', $offer), [
            'use_wallet' => false,
            'payment_intent_id' => 'pi_123',
        ]);

        $transaction = $offer->refresh()->transaction;
        $response->assertRedirect(route('checkout.success', $transaction));

        $this->assertDatabaseHas('transactions', [
            'offer_id' => $offer->id, 'amount' => 50.00, 'wallet_amount' => 0, 'card_amount' => 50.00, 'status' => 'payment_received',
        ]);
        $this->assertEquals(ItemStatus::SOLD, $item->fresh()->status);
        $this->assertEquals('paid', $offer->fresh()->status);
        $this->assertEquals(0, $buyer->fresh()->wallet);
    }

    #[Test]
    public function processes_a_successful_payment_using_wallet_only(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create(['wallet' => 100.00]);
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'amount' => 50.00,
            'status' => 'accepted',
        ]);

        $response = $this->actingAs($buyer)->post(route('payment.store', $offer), [
            'use_wallet' => true,
        ]);

        $transaction = $offer->refresh()->transaction;
        $response->assertRedirect(route('checkout.success', $transaction));

        $this->assertDatabaseHas('transactions', [
            'offer_id' => $offer->id, 'amount' => 50.00, 'wallet_amount' => 50.00, 'card_amount' => 0,
        ]);
        $this->assertEquals(50.00, $buyer->fresh()->wallet);
    }

    #[Test]
    public function processes_a_successful_mixed_payment(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create(['wallet' => 30.00]);
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'amount' => 100.00,
            'status' => 'accepted',
        ]);

        Mockery::mock('alias:' . PaymentIntent::class)
            ->shouldReceive('retrieve')
            ->once()
            ->with('pi_456')
            ->andReturn((object)[
                'id' => 'pi_456',
                'status' => 'succeeded',
                'amount' => 7000,
            ]);

        $response = $this->actingAs($buyer)->post(route('payment.store', $offer), [
            'use_wallet' => true,
            'payment_intent_id' => 'pi_456',
        ]);

        $transaction = $offer->refresh()->transaction;
        $response->assertRedirect(route('checkout.success', $transaction));

        $this->assertDatabaseHas('transactions', [
            'offer_id' => $offer->id, 'amount' => 100.00, 'wallet_amount' => 30.00, 'card_amount' => 70.00,
        ]);
        $this->assertEquals(0, $buyer->fresh()->wallet);
    }

    #[Test]
    public function fails_payment_if_stripe_verification_fails(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'amount' => 50.00, 'status' => 'accepted']);

        Mockery::mock('alias:' . PaymentIntent::class)
            ->shouldReceive('retrieve')
            ->once()
            ->with('pi_789')
            ->andReturn((object)[
                'id' => 'pi_789',
                'status' => 'failed',
                'amount' => 5000,
            ]);

        $response = $this->actingAs($buyer)->post(route('payment.store', $offer), [
            'payment_intent_id' => 'pi_789',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('payment');
        $this->assertDatabaseMissing('transactions', ['offer_id' => $offer->id]);
    }

    #[Test]
    public function fails_payment_if_stripe_amount_mismatches(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create(['user_id' => $buyer->id, 'item_id' => $item->id, 'amount' => 50.00, 'status' => 'accepted']);

        Mockery::mock('alias:' . PaymentIntent::class)
            ->shouldReceive('retrieve')
            ->once()
            ->with('pi_101')
            ->andReturn((object)[
                'id' => 'pi_101',
                'status' => 'succeeded',
                'amount' => 4000, // Incorrect amount
            ]);

        $response = $this->actingAs($buyer)->post(route('payment.store', $offer), [
            'payment_intent_id' => 'pi_101',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('payment');
        $this->assertDatabaseMissing('transactions', ['offer_id' => $offer->id]);
    }
}
