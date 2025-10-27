<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que la page d'accueil se charge correctement et affiche les articles.
     */
    #[Test]
    public function welcome_page_loads_successfully_and_displays_items(): void
    {
        $user = User::factory()->create();
        $items = Item::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->get(route('welcome'));

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('items');

        $viewItems = $response->viewData('items');
        $this->assertCount(3, $viewItems);
        $this->assertEquals($items->sortByDesc('created_at')->pluck('id'), $viewItems->pluck('id'));
    }

    /**
     * Test que la page de détail d'un article se charge correctement.
     */
    #[Test]
    public function item_show_page_loads_successfully(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('items.show', $item));

        $response->assertStatus(200);
        $response->assertViewIs('items.show');
        $response->assertViewHas('item', $item);
    }
}
