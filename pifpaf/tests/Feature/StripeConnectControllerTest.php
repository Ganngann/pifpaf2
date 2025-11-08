<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Mockery;
use Stripe\Account;
use Stripe\AccountSession;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class StripeConnectControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Clean up the testing environment before the next test.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_a_stripe_account_and_returns_an_account_session_for_a_new_user(): void
    {
        // Arrange
        $user = User::factory()->create(['stripe_account_id' => null]);
        $this->assertNull($user->stripe_account_id);

        // Mock Stripe SDK static calls
        $mockAccount = Mockery::mock('alias:' . Account::class);
        $mockAccount->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($user) {
                return $arg['type'] === 'express' && $arg['email'] === $user->email;
            }))
            ->andReturn((object)['id' => 'acct_123456789']);

        $mockAccountSession = Mockery::mock('alias:' . AccountSession::class);
        $mockAccountSession->shouldReceive('create')
            ->once()
            ->with([
                'account' => 'acct_123456789',
                'components' => [
                    'account_onboarding' => [
                        'enabled' => true,
                    ],
                ],
            ])
            ->andReturn((object)['client_secret' => 'as_secret_123456789']);

        // Act
        $response = $this->actingAs($user)->get(route('stripe.connect.account_session'));

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['client_secret' => 'as_secret_123456789']);

        $user->refresh();
        $this->assertEquals('acct_123456789', $user->stripe_account_id);
    }

    #[Test]
    public function it_returns_an_account_session_for_an_existing_user(): void
    {
        // Arrange
        $user = User::factory()->create(['stripe_account_id' => 'acct_existing_123']);

        // Mock Stripe SDK static calls
        // We do NOT mock Account::create, so an error will be thrown if it's called.
        $mockAccountSession = Mockery::mock('alias:' . AccountSession::class);
        $mockAccountSession->shouldReceive('create')
            ->once()
            ->with([
                'account' => 'acct_existing_123',
                'components' => [
                    'account_onboarding' => [
                        'enabled' => true,
                    ],
                ],
            ])
            ->andReturn((object)['client_secret' => 'as_secret_987654321']);

        // Act
        $response = $this->actingAs($user)->get(route('stripe.connect.account_session'));

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['client_secret' => 'as_secret_987654321']);
    }
}
