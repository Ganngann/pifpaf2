<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprimer les anciennes données pour éviter les doublons
        User::query()->delete();
        Item::query()->delete();

        // Création d'un utilisateur pour lier les articles
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

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
}
