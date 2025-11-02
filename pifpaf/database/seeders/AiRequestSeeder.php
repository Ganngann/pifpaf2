<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AiRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class AiRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Assurez-vous que l'utilisateur de test existe
        $user = User::where('email', 'test@example.com')->first();
        if (!$user) {
            $this->command->error('Test user not found. Please run DatabaseSeeder first.');
            return;
        }

        // Créez un fichier image factice
        Storage::disk('public')->makeDirectory('ai_images');
        $placeholderPath = resource_path('images/placeholder_tshirt.jpg');
        if (!file_exists($placeholderPath)) {
            $this->command->error('Placeholder image not found at ' . $placeholderPath);
            // Créez une image vide si elle n'existe pas pour éviter l'échec
            $img = imagecreatetruecolor(800, 600);
            imagefill($img, 0, 0, imagecolorallocate($img, 255, 255, 255));
            imagejpeg($img, $placeholderPath);
            imagedestroy($img);
            $this->command->info('Created a dummy placeholder image.');
        }

        $imagePath = Storage::disk('public')->putFile('ai_images', new File($placeholderPath));


        // Créer la requête IA
        AiRequest::create([
            'user_id' => $user->id,
            'image_path' => $imagePath,
            'status' => 'completed',
            'result' => json_decode('[
                {
                    "box": { "x1": 250, "y1": 150, "x2": 750, "y2": 850 },
                    "title": "T-shirt blanc",
                    "category": "Vêtements",
                    "description": "Un t-shirt blanc simple en coton.",
                    "price": 15.99
                },
                {
                    "box": { "x1": 50, "y1": 400, "x2": 200, "y2": 600 },
                    "title": "Tasse à café",
                    "category": "Maison",
                    "description": "Une tasse à café en céramique.",
                    "price": 8.50
                }
            ]', true),
        ]);

        $this->command->info('AI request seeder ran successfully!');
    }
}
