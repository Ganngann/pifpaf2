<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;

class VerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer un utilisateur spécifique pour le test
        $user = User::factory()->create([
            'email' => 'seller@example.com',
        ]);

        // Créer une annonce avec 3 images pour cet utilisateur
        Item::factory()
            ->for($user)
            ->has(
                ItemImage::factory()
                    ->count(3)
                    ->sequence(
                        ['is_primary' => true, 'order' => 0],
                        ['is_primary' => false, 'order' => 1],
                        ['is_primary' => false, 'order' => 2]
                    ),
                'images' // Spécifier le nom de la relation ici
            )
            ->create();
    }
}
