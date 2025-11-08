<?php

namespace Tests\Feature;

use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Item;
use Tests\TestCase;

class DistanceFilterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste le filtrage des annonces par distance.
     */
    public function test_items_can_be_filtered_by_distance(): void
    {
        // 1. Préparation
        $user = User::factory()->create();

        // Adresse proche (Paris, ~8km de "Paris Centre")
        $addressNear = Address::factory()->create([
            'user_id' => $user->id,
            'latitude' => 48.858844,
            'longitude' => 2.294351, // Tour Eiffel
        ]);
        $itemNear = Item::factory()->create([
            'user_id' => $user->id,
            'address_id' => $addressNear->id,
            'pickup_available' => true,
            'status' => 'available',
        ]);

        // Adresse lointaine (Lyon, >300km de "Paris Centre")
        $addressFar = Address::factory()->create([
            'user_id' => $user->id,
            'latitude' => 45.757994,
            'longitude' => 4.832011, // Lyon
        ]);
        $itemFar = Item::factory()->create([
            'user_id' => $user->id,
            'address_id' => $addressFar->id,
            'pickup_available' => true,
        ]);

        // Un article non disponible pour le retrait sur place
        $itemNoPickup = Item::factory()->create(['pickup_available' => false]);


        // 2. Mocking de l'API de géocodage
        // On simule une recherche pour "Paris"
        Http::fake([
            'geocode.maps.co/search*' => Http::response([
                [
                    'lat' => '48.856614', // Coordonnées du centre de Paris
                    'lon' => '2.3522219',
                ]
            ], 200),
        ]);

        // 3. Action
        // On effectue une recherche autour de "Paris" dans un rayon de 10km
        $response = $this->get(route('welcome', [
            'location' => 'Paris',
            'distance' => '10',
        ]));

        // 4. Assertions
        $response->assertStatus(200);
        $response->assertSee($itemNear->title); // L'article proche doit être visible
        $response->assertDontSee($itemFar->title); // L'article lointain ne doit pas être visible
        $response->assertDontSee($itemNoPickup->title); // L'article sans retrait ne doit pas être visible
    }
}
