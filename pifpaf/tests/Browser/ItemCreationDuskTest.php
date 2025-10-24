<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ItemCreationDuskTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Teste la création d'une annonce via le formulaire du navigateur et prend des captures d'écran.
     */
    public function testUserCanCreateItemAndTakeScreenshots(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/dashboard')
                    ->clickLink('Vendre un article')
                    ->assertPathIs('/items/create')
                    ->screenshot('sprint_1/user_story_3/01_formulaire_creation_annonce') // Capture du formulaire vide
                    ->type('title', 'Superbe Lampe Vintage')
                    ->type('description', 'Une lampe unique pour décorer votre intérieur.')
                    ->type('price', '75.50')
                    ->attach('image', __DIR__.'/screenshots/test-image.jpg')
                    ->screenshot('sprint_1/user_story_3/02_formulaire_rempli') // Capture du formulaire rempli
                    ->press('Mettre en vente')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Annonce créée avec succès.')
                    ->screenshot('sprint_1/user_story_3/03_succes_creation'); // Capture du message de succès
        });
    }
}
