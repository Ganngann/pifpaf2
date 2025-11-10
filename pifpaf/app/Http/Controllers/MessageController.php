<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Notifications\NewMessageNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    use AuthorizesRequests;

    /**
     * Enregistre un nouveau message dans une conversation.
     */
    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $request->validate([
            'content' => 'required|string',
        ]);

        $message = $conversation->messages()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // Notifier l'autre participant de la conversation
        $recipient = $conversation->buyer_id === Auth::id() ? $conversation->seller : $conversation->buyer;
        if ($recipient->wantsNotification('new_message')) {
            $recipient->notify(new NewMessageNotification($message));
        }

        return back();
    }
}
