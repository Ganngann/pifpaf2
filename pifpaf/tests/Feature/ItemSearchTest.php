<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Création d'un utilisateur pour lier les articles
        $user = User::factory()->create();

        // Création de données de test
        Item::factory()->create([
            'title' => 'Super T-Shirt Rouge',
            'description' => 'Un t-shirt confortable en coton.',
            'category' => 'Vêtements',
            'price' => 25.50,
            'user_id' => $user->id,
        ]);

        Item::factory()->create([
            'title' => 'Smartphone Android performant',
            'description' => 'Un téléphone avec un excellent appareil photo.',
            'category' => 'Électronique',
            'price' => 450.00,
            'user_id' => $user->id,
        ]);

        Item::factory()->create([
            'title' => 'Canapé en cuir',
            'description' => 'Un grand canapé pour toute la famille.',
            'category' => 'Maison',
            'price' => 899.99,
            'user_id' => $user->id,
        ]);

        Item::factory()->create([
            'title' => 'VTT Rouge',
            'description' => 'Un vélo tout-terrain pour les amateurs de sport.',
            'category' => 'Sport',
            'price' => 320.00,
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function on_peut_rechercher_des_articles_par_mot_cle_dans_le_titre()
    {
        $response = $this->get('/?search=Smartphone');

        $response->assertStatus(200);
        $response->assertSee('Smartphone Android performant');
        $response->assertDontSee('Super T-Shirt Rouge');
    }

    #[Test]
    public function on_peut_filtrer_les_articles_par_categorie()
    {
        $response = $this->get('/?category=Vêtements');

        $response->assertStatus(200);
        $response->assertSee('Super T-Shirt Rouge');
        $response->assertDontSee('Smartphone Android performant');
    }

    #[Test]
    public function on_peut_filtrer_les_articles_par_prix_minimum()
    {
        $response = $this->get('/?min_price=500');

        $response->assertStatus(200);
        $response->assertSee('Canapé en cuir');
        $response->assertDontSee('Smartphone Android performant');
    }

    #[Test]
    public function on_peut_filtrer_les_articles_par_prix_maximum()
    {
        $response = $this->get('/?max_price=100');

        $response->assertStatus(200);
        $response->assertSee('Super T-Shirt Rouge');
        $response->assertDontSee('Smartphone Android performant');
    }

    #[Test]
    public function on_peut_combiner_la_recherche_et_les_filtres()
    {
        $response = $this->get('/?search=Rouge&category=Sport&min_price=300&max_price=400');

        $response->assertStatus(200);
        $response->assertSee('VTT Rouge');
        $response->assertDontSee('Super T-Shirt Rouge');
        $response->assertDontSee('Smartphone Android performant');
    }

    #[Test]
    public function un_message_saffiche_si_aucun_article_ne_correspond_a_la_recherche()
    {
        $response = $this->get('/?search=Inexistant');

        $response->assertStatus(200);

        // Utilisation de str_contains pour contourner les problèmes d'encodage de l'assertion
        $this->assertTrue(
            str_contains($response->getContent(), "Aucun article trouvé. Essayez d'ajuster vos filtres de recherche.")
        );
    }
}
