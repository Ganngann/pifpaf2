<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    private $seller;
    private $buyer;
    private $transaction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create();
        $this->buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $this->seller->id]);
        $offer = Offer::factory()->create(['user_id' => $this->buyer->id, 'item_id' => $item->id]);
        $this->transaction = Transaction::factory()->create(['offer_id' => $offer->id, 'status' => 'completed']);
    }

    #[Test]
    public function buyer_can_leave_a_review_on_a_completed_transaction()
    {
        $this->actingAs($this->buyer);
        $reviewData = ['rating' => 5, 'comment' => 'Excellent transaction!'];

        $response = $this->post(route('reviews.store', $this->transaction), $reviewData);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('reviews', [
            'transaction_id' => $this->transaction->id,
            'reviewer_id' => $this->buyer->id,
            'reviewee_id' => $this->seller->id,
            'rating' => 5,
            'comment' => 'Excellent transaction!',
        ]);
    }

    #[Test]
    public function seller_can_leave_a_review_on_a_completed_transaction()
    {
        $this->actingAs($this->seller);
        $reviewData = ['rating' => 4, 'comment' => 'Great buyer.'];

        $response = $this->post(route('reviews.store', $this->transaction), $reviewData);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('reviews', [
            'transaction_id' => $this->transaction->id,
            'reviewer_id' => $this->seller->id,
            'reviewee_id' => $this->buyer->id,
            'rating' => 4,
            'comment' => 'Great buyer.',
        ]);
    }

    #[Test]
    public function user_cannot_leave_a_review_on_an_incomplete_transaction()
    {
        $this->transaction->update(['status' => 'payment_received']);
        $this->actingAs($this->buyer);
        $reviewData = ['rating' => 5];

        $response = $this->post(route('reviews.store', $this->transaction), $reviewData);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('reviews', ['transaction_id' => $this->transaction->id]);
    }

    #[Test]
    public function user_cannot_leave_a_review_on_a_transaction_they_are_not_part_of()
    {
        $unrelatedUser = User::factory()->create();
        $this->actingAs($unrelatedUser);
        $reviewData = ['rating' => 5];

        $response = $this->post(route('reviews.store', $this->transaction), $reviewData);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('reviews', ['transaction_id' => $this->transaction->id]);
    }

    #[Test]
    public function user_cannot_leave_two_reviews_for_the_same_transaction()
    {
        $this->actingAs($this->buyer);
        $reviewData = ['rating' => 5];
        $this->post(route('reviews.store', $this->transaction), $reviewData);

        // Attempt to post a second review
        $response = $this->post(route('reviews.store', $this->transaction), ['rating' => 1]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, $this->transaction->reviews()->count());
    }

    #[Test]
    public function rating_is_required_and_must_be_between_1_and_5()
    {
        $this->actingAs($this->buyer);

        // Missing rating
        $response = $this->post(route('reviews.store', $this->transaction), ['comment' => 'No rating']);
        $response->assertSessionHasErrors('rating');

        // Rating too low
        $response = $this->post(route('reviews.store', $this->transaction), ['rating' => 0]);
        $response->assertSessionHasErrors('rating');

        // Rating too high
        $response = $this->post(route('reviews.store', $this->transaction), ['rating' => 6]);
        $response->assertSessionHasErrors('rating');

        $this->assertDatabaseMissing('reviews', ['transaction_id' => $this->transaction->id]);
    }
}
