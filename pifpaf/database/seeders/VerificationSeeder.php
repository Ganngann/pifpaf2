<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class VerificationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $item = Item::factory()->create(['user_id' => $user->id]);

        // Create dummy images and corresponding records
        $imagePaths = ['image1.jpg', 'image2.jpg', 'image3.jpg'];
        foreach ($imagePaths as $index => $path) {
            // Ensure the directory exists
            Storage::disk('public')->makeDirectory('item_images');

            // Create a fake file and store it
            $fakeImage = UploadedFile::fake()->image($path);
            $storedPath = $fakeImage->store('item_images', 'public');

            ItemImage::factory()->create([
                'item_id' => $item->id,
                'path' => $storedPath,
                'is_primary' => ($index == 2), // Make the last image primary initially
                'order' => $index,
            ]);
        }
    }
}