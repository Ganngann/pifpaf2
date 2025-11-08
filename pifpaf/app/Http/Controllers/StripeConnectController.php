<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Exception\ApiErrorException;

class StripeConnectController extends Controller
{
    /**
     * Create a Stripe account for the user if they don't have one,
     * and return an account session client secret.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAccountSession(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $user = Auth::user();

        try {
            // Create a Stripe account for the user if they don't have one.
            if (!$user->stripe_account_id) {
                $account = \Stripe\Account::create([
                    'type' => 'express',
                    'country' => 'FR',
                    'email' => $user->email,
                    'capabilities' => [
                        'card_payments' => ['requested' => true],
                        'transfers' => ['requested' => true],
                    ],
                ]);

                $user->stripe_account_id = $account->id;
                $user->save();
            }

            // Create an account session to initialize the onboarding component.
            $accountSession = \Stripe\AccountSession::create([
                'account' => $user->stripe_account_id,
                'components' => [
                    'account_onboarding' => [
                        'enabled' => true,
                    ],
                ],
            ]);

            return response()->json([
                'client_secret' => $accountSession->client_secret,
            ]);

        } catch (ApiErrorException $e) {
            // In a real app, you'd want to log this error.
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
