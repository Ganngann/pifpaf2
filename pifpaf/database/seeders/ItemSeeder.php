<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Storage::fake('public');

        // Ensure we have a consistent user for our test items
        $seller = User::factory()->create([
            'name' => 'Seller User',
            'email' => 'seller@example.com',
        ]);

        $buyer = User::factory()->create([
            'name' => 'Buyer User',
            'email' => 'buyer@example.com',
        ]);

        // Create 5 items for the seller
        Item::factory(5)->for($seller)->create()->each(function ($item) {
            // For each item, create between 1 and 3 images
            $imageCount = rand(1, 3);
            for ($i = 0; $i < $imageCount; $i++) {
                $imagePath = UploadedFile::fake()->image("item_{$item->id}_image_{$i}.jpg")->store("item_images", 'public');

                ItemImage::factory()->create([
                    'item_id' => $item->id,
                    'path' => $imagePath,
                    'is_primary' => ($i === 0), // First image is primary
                    'order' => $i,
                ]);
            }
        });
    }
}
