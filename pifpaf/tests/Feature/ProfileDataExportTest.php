<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileDataExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_export_their_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertHeader('Content-Disposition', 'attachment; filename="pifpaf_user_data.json"');

        $data = json_decode($response->getContent(), true);

        $this->assertEquals($user->id, $data['profile']['id']);
    }
}
