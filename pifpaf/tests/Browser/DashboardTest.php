<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\DuskTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    #[Group('dashboard')]
    public function seller_can_see_their_own_items_on_dashboard(): void
    {
        $user1 = User::factory()->create();
        $item1 = Item::factory()->for($user1, 'user')->create(['title' => 'Mon premier objet']);
        $item2 = Item::factory()->for($user1, 'user')->create(['title' => 'Mon deuxième objet']);

        $user2 = User::factory()->create();
        $item3 = Item::factory()->for($user2, 'user')->create(['title' => 'Objet d\'un autre vendeur']);


        $this->browse(function (Browser $browser) use ($user1, $item1, $item2, $item3) {
            $browser->loginAs($user1)
                    ->visit('/dashboard')
                    ->assertSee($item1->title)
                    ->assertSee($item2->title)
                    ->assertDontSee($item3->title);
        });
    }

    #[Test]
    #[Group('dashboard')]
    public function dashboard_shows_prompt_when_user_has_no_items(): void
    {
        $userWithoutItems = User::factory()->create();

        $this->browse(function (Browser $browser) use ($userWithoutItems) {
            $browser->loginAs($userWithoutItems)
                    ->visit('/dashboard')
                    ->assertSee('Vous n\'avez pas encore d\'annonce.')
                    ->assertSeeLink('Créer ma première annonce');
        });
    }
}
