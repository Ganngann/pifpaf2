<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Enums\ItemStatus;
use App\Models\ItemImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ItemSoldPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function sold_item_page_displays_sold_banner_and_hides_actions(): void
    {
        // Arrange: Create a user and a sold item with a primary image
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => ItemStatus::SOLD, 'user_id' => $user->id]);
        ItemImage::factory()->create(['item_id' => $item->id, 'is_primary' => true]);
        $viewer = User::factory()->create();

        // Act: View the item page as another user
        $response = $this->actingAs($viewer)->get(route('items.show', $item));

        // Assert: Check for "VENDU" banner and absence of action buttons
        $response->assertStatus(200);
        $response->assertSee('VENDU');
        $response->assertDontSee('Acheter');
        $response->assertDontSee('Contacter le vendeur');
        // Check that the offer button is not present using a direct PHPUnit regex assertion on the response content
        $this->assertDoesNotMatchRegularExpression('/<button[^>]*dusk="submit-offer-button"[^>]*>/', $response->getContent());
    }

    #[Test]
    public function available_item_page_displays_actions_and_no_banner(): void
    {
        // Arrange: Create a user and an available item with a primary image and delivery options
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'status' => ItemStatus::AVAILABLE,
            'user_id' => $user->id,
            'pickup_available' => true,
            'delivery_available' => true
        ]);
        ItemImage::factory()->create(['item_id' => $item->id, 'is_primary' => true]);
        $viewer = User::factory()->create();

        // Act: View the item page as another user
        $response = $this->actingAs($viewer)->get(route('items.show', $item));

        // Assert: Check for the absence of "VENDU" banner and presence of action buttons
        $response->assertStatus(200);
        $response->assertDontSee('VENDU');
        $response->assertSee('Acheter');
        $response->assertSee('Contacter le vendeur');
        // Use a direct PHPUnit regex assertion on the response content for robustness
        $this->assertMatchesRegularExpression('/<button[^>]*dusk="submit-offer-button"[^>]*>[\s\r\n]*Envoyer l\'offre[\s\r\n]*<\/button>/', $response->getContent());
    }
}
