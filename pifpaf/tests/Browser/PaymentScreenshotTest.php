<?php

namespace Tests\Browser;

use App\Models\Item;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Test;

class PaymentScreenshotTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function capture_payment_flow_screenshots()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create(['wallet' => 10.00]);
        $item = Item::factory()->create(['user_id' => $seller->id, 'price' => 25.00]);
        $offer = Offer::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'amount' => 20.00,
            'status' => 'accepted'
        ]);

        $this->browse(function (Browser $browser) use ($buyer, $offer) {
            $browser->loginAs($buyer)
                ->visit(route('payment.create', $offer))
                ->assertSee('Récapitulatif de la commande');

            // Desktop screenshot
            $browser->resize(1280, 800)
                ->screenshot('US-TRS-1/payment-desktop-initial');

            // Desktop with wallet checked
            $browser->check('use_wallet')
                ->screenshot('US-TRS-1/payment-desktop-wallet-checked');

            // Mobile screenshot
            $browser->resize(375, 812)
                 ->screenshot('US-TRS-1/payment-mobile-initial');

            // Mobile with wallet checked
            $browser->check('use_wallet')
                ->screenshot('US-TRS-1/payment-mobile-wallet-checked');
        });
    }
}
