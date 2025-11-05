<?php

namespace Tests\Feature\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the public profile page for a user is displayed correctly.
     *
     * @return void
     */
    public function test_public_profile_page_is_displayed_correctly(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create items for the user
        $item1 = Item::factory()->create(['user_id' => $user->id, 'title' => 'Mon premier objet']);
        $item2 = Item::factory()->create(['user_id' => $user->id, 'title' => 'Mon deuxième objet']);

        // Access the user's profile page
        $response = $this->get(route('profile.show', $user));

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the user's name is visible
        $response->assertSee($user->name);

        // Assert that the user's items are visible
        $response->assertSee($item1->title);
        $response->assertSee($item2->title);
    }

    /**
     * Test that the profile page shows a message when the user has no items.
     *
     * @return void
     */
    public function test_profile_page_shows_message_when_user_has_no_items(): void
    {
        // Create a user without items
        $user = User::factory()->create();

        // Access the user's profile page
        $response = $this->get(route('profile.show', $user));

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the user's name is visible
        $response->assertSee($user->name);

        // Assert that the "no items" message is visible by checking the raw content
        $this->assertTrue(
            str_contains($response->getContent(), 'Aucun article trouvé.')
        );
    }
}
