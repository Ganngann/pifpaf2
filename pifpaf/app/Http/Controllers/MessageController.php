<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
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

        $conversation->messages()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return back();
    }
}
