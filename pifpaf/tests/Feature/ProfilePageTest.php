<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Review;
use PHPUnit\Framework\Attributes\Test;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_user_average_rating_and_reviews()
    {
        // 1. Arrange
        // Créer l'utilisateur vendeur (celui qui est noté)
        $reviewee = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $reviewee->id]);

        // Créer le premier acheteur et sa transaction/avis
        $reviewer1 = User::factory()->create();
        $offer1 = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $reviewer1->id]);
        $transaction1 = Transaction::factory()->create(['offer_id' => $offer1->id]);
        $review1 = Review::factory()->create([
            'transaction_id' => $transaction1->id,
            'reviewee_id' => $reviewee->id,
            'reviewer_id' => $reviewer1->id,
            'rating' => 4,
            'comment' => 'Super vendeur, communication au top !',
        ]);

        // Créer le second acheteur et sa transaction/avis
        $reviewer2 = User::factory()->create();
        $offer2 = Offer::factory()->create(['item_id' => $item->id, 'user_id' => $reviewer2->id]);
        $transaction2 = Transaction::factory()->create(['offer_id' => $offer2->id]);
        $review2 = Review::factory()->create([
            'transaction_id' => $transaction2->id,
            'reviewee_id' => $reviewee->id,
            'reviewer_id' => $reviewer2->id,
            'rating' => 5,
            'comment' => 'Transaction parfaite, je recommande.',
        ]);

        // 2. Act
        $response = $this->get(route('profile.show', $reviewee));

        // 3. Assert
        $response->assertStatus(200);

        // Vérifier la note moyenne et le nombre d'avis
        $response->assertSee('Note moyenne :');
        $response->assertSee('4.5 / 5');
        $response->assertSee('(2 avis)');

        // Vérifier que les détails des avis sont affichés
        $response->assertSee($reviewer1->name);
        $response->assertSee('4/5');
        $response->assertSee('Super vendeur, communication au top !');

        $response->assertSee($reviewer2->name);
        $response->assertSee('5/5');
        $response->assertSee('Transaction parfaite, je recommande.');
    }

    #[Test]
    public function it_displays_a_message_when_there_are_no_reviews()
    {
        // 1. Arrange
        $userWithoutReviews = User::factory()->create();

        // 2. Act
        $response = $this->get(route('profile.show', $userWithoutReviews));

        // 3. Assert
        $response->assertStatus(200);
        $response->assertSee('Aucun avis pour le moment.');
        $response->assertDontSee('Note moyenne :');
    }
}
