<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste l'affichage de la page de détail d'un article.
     *
     * @return void
     */
    #[Test]
    public function item_detail_page_is_displayed_correctly()
    {
        // 1. Créer un utilisateur (vendeur) et un article
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        // 2. Accéder à la page de détail de l'article
        $response = $this->get(route('items.show', $item));

        // 3. Vérifier que la page s'affiche correctement
        $response->assertStatus(200);

        // 4. Vérifier que les informations de l'article et du vendeur sont présentes
        $response->assertSee($item->title);
        $response->assertSee($item->description);
        $response->assertSee(number_format($item->price, 2, ',', ' '));
        $response->assertSee($user->name);
    }
}
