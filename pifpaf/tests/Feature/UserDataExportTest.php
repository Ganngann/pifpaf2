<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDataExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_export_their_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="pifpaf_user_data_' . $user->id . '.json"');

        $data = $response->json();

        $this->assertArrayHasKey('profile', $data);
        $this->assertArrayHasKey('salesTransactions', $data);
        $this->assertArrayHasKey('purchaseTransactions', $data);
        $this->assertEquals($user->email, $data['profile']['email']);
    }
}
