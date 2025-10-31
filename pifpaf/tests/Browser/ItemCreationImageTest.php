<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ItemCreationImageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Teste si les prévisualisations des images s'affichent lors de la sélection.
     *
     * @return void
     */
    public function test_image_previews_are_shown_on_selection(): void
    {
        $user = User::factory()->create();

        // Créer de vrais fichiers temporaires pour le navigateur
        $path1 = storage_path('app/public/temp_for_test_1.jpg');
        $path2 = storage_path('app/public/temp_for_test_2.png');
        touch($path1);
        touch($path2);

        $this->browse(function (Browser $browser) use ($user, $path1, $path2) {
            $browser->loginAs($user)
                    ->visit('/items/create')
                    ->attach('images[]', $path1)
                    ->attach('images[]', $path2)
                    ->pause(500) // Laisser le temps au JS de créer les previews
                    ->within('#image-preview-container', function ($browser) {
                        $browser->assertCount('img', 2);
                    });
        });

        // Nettoyer les fichiers temporaires
        unlink($path1);
        unlink($path2);
    }
}
