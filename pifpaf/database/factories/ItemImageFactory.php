<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemImage>
 */
class ItemImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Créer un nom de fichier unique pour l'image factice
        $fileName = 'item_images_factory/' . uniqid() . '.jpg';
        $storagePath = storage_path('app/public/' . $fileName);

        // S'assurer que le répertoire existe
        $directory = dirname($storagePath);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Créer une image factice avec GD
        $image = imagecreatetruecolor(640, 480);
        $backgroundColor = imagecolorallocate($image, 230, 230, 230); // Gris clair
        $textColor = imagecolorallocate($image, 50, 50, 50); // Gris foncé
        imagefill($image, 0, 0, $backgroundColor);
        imagestring($image, 5, 280, 230, '640x480', $textColor);
        imagejpeg($image, $storagePath);
        imagedestroy($image);

        return [
            'item_id' => Item::factory(),
            'path' => $fileName,
            'is_primary' => false,
            'order' => 0,
        ];
    }
}
