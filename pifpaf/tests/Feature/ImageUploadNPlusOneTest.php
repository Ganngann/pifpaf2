<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadNPlusOneTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_upload_n_plus_one()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $images = [];
        for ($i = 0; $i < 5; $i++) {
            $images[] = UploadedFile::fake()->image('image'.$i.'.jpg');
        }

        DB::enableQueryLog();
        $response = $this->put(route('items.update', $item), [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'category' => 'Autre',
            'price' => 10,
            'images' => $images,
        ]);
        $response->assertRedirect();

        $queries = DB::getQueryLog();
        $countQueries = 0;
        foreach ($queries as $query) {
            if (strpos($query['query'], 'select count(*) as aggregate from "item_images"') !== false) {
                $countQueries++;
            }
        }

        $this->assertTrue($countQueries <= 1, "Too many count queries: " . $countQueries);
    }
}
