<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\ItemImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'category' => $this->faker->randomElement(['Vêtements', 'Électronique', 'Maison', 'Sport', 'Loisirs']),
            'price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }

}
