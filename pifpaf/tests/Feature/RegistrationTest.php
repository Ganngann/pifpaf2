<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_new_user_can_register()
    {
        // Données du formulaire d'inscription
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Envoyer une requête POST pour simuler l'inscription
        $response = $this->post('/register', $userData);

        // Vérifier que la redirection est correcte après l'inscription
        $response->assertRedirect('/dashboard');

        // Vérifier que l'utilisateur est bien authentifié
        $this->assertAuthenticated();

        // Vérifier que l'utilisateur a été créé dans la base de données
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);
    }
}
