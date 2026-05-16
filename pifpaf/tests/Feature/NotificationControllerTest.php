<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Notifications\NewOfferNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_notifications()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertViewIs('notifications.index');
    }

    public function test_user_can_mark_notification_as_read()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        $offer = Offer::factory()->create(['item_id' => $item->id]);

        $user->notify(new NewOfferNotification($offer));
        $notification = $user->notifications()->first();

        $response = $this->actingAs($user)->patch(route('notifications.read', $notification->id));
        $response->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }
}
