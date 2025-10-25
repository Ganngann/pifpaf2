<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellerDashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test the seller dashboard, item editing, and item deletion.
     *
     * @return void
     */
    public function testSellerDashboardAndItemManagement()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($user, $item) {
            // Login and visit dashboard
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->assertSee('Tableau de bord Vendeur')
                    ->assertSee($item->title);

            // Desktop Screenshot
            $browser->screenshot('dashboard-desktop');

            // Mobile Screenshot
            $browser->resize(375, 812)
                    ->screenshot('dashboard-mobile-responsive');

            // Test Edit
            $browser->clickLink('Modifier')
                    ->assertPathIs('/items/' . $item->id . '/edit')
                    ->waitForText('Modifier l\'annonce', 5)
                    ->type('title', 'Nouveau Titre')
                    ->press('button[type="submit"]')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Annonce modifiée avec succès.')
                    ->assertSee('Nouveau Titre');

            // Test Delete
            $browser->press('Supprimer')
                    ->acceptDialog()
                    ->assertPathIs('/dashboard')
                    ->assertSee('Annonce supprimée avec succès.')
                    ->assertDontSee('Nouveau Titre');
        });
    }
}
