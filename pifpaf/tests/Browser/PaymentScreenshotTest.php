<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class PaymentScreenshotTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function capture_payment_flow_screenshots()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'status' => 'accepted'
        ]);

        $this->browse(function (Browser $browser) use ($buyer, $offer) {
            $browser->loginAs($buyer)
                ->visit('/dashboard');

            // Desktop Screenshot
            $browser->resize(1920, 1080)
                ->screenshot('us13-desktop-dashboard-payment-link');

            $browser->clickLink('Payer')
                ->waitFor('#card_number')
                ->screenshot('us13-desktop-payment-form');

            // Mobile Screenshot
            $browser->resize(375, 812)
                ->visit('/dashboard')
                ->screenshot('us13-mobile-dashboard-payment-link');

            $browser->clickLink('Payer')
                ->waitFor('#card_number')
                ->screenshot('us13-mobile-payment-form');
        });
    }
}
