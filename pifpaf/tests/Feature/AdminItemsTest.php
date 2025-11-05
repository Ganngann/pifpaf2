<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminItemsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function non_admins_cannot_access_the_admin_items_index()
    {
        $this->actingAs($this->user)
             ->get(route('admin.items.index'))
             ->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_the_admin_items_index()
    {
        $this->actingAs($this->admin)
             ->get(route('admin.items.index'))
             ->assertStatus(200)
             ->assertViewIs('admin.items.index');
    }

    /** @test */
    public function admin_can_view_a_list_of_items()
    {
        $item1 = Item::factory()->create(['title' => 'Item Alpha']);
        $item2 = Item::factory()->create(['title' => 'Item Beta']);

        $this->actingAs($this->admin)
             ->get(route('admin.items.index'))
             ->assertSee('Item Alpha')
             ->assertSee('Item Beta');
    }

    /** @test */
    public function admin_can_search_for_items_by_title()
    {
        Item::factory()->create(['title' => 'Specific Search Term']);
        Item::factory()->create(['title' => 'Another Item']);

        $this->actingAs($this->admin)
             ->get(route('admin.items.index', ['search' => 'Specific']))
             ->assertSee('Specific Search Term')
             ->assertDontSee('Another Item');
    }

    /** @test */
    public function admin_can_search_for_items_by_seller_name()
    {
        $seller = User::factory()->create(['name' => 'John Doe']);
        Item::factory()->create(['user_id' => $seller->id, 'title' => 'Item from John']);
        Item::factory()->create(['title' => 'Anonymous Item']);

        $this->actingAs($this->admin)
             ->get(route('admin.items.index', ['search' => 'John Doe']))
             ->assertSee('Item from John')
             ->assertDontSee('Anonymous Item');
    }

    /** @test */
    public function admin_can_delete_an_item()
    {
        $item = Item::factory()->create();

        $this->actingAs($this->admin)
             ->delete(route('admin.items.destroy', $item));

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    /** @test */
    public function non_admins_cannot_delete_an_item()
    {
        $item = Item::factory()->create();

        $this->actingAs($this->user)
             ->delete(route('admin.items.destroy', $item))
             ->assertStatus(403);

        $this->assertDatabaseHas('items', ['id' => $item->id]);
    }
}
