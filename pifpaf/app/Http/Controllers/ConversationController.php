<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Item;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Affiche la liste des conversations de l'utilisateur.
     */
    public function index()
    {
        $user = Auth::user();

        $conversations = Conversation::where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with(['item.primaryImage', 'latestMessage', 'buyer', 'seller'])
            ->get()
            // Trier pour que les conversations avec le message le plus récent apparaissent en premier
            ->sortByDesc(function ($conversation) {
                return $conversation->latestMessage ? $conversation->latestMessage->created_at : $conversation->updated_at;
            });


        return view('conversations.index', compact('conversations'));
    }

    /**
     * Affiche une conversation spécifique.
     */
    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        // Marquer les messages comme lus pour l'utilisateur actuel
        $conversation->messages()
            ->where('user_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('conversations.show', compact('conversation'));
    }

    /**
     * Crée une nouvelle conversation ou redirige vers une conversation existante.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);

        $item = Item::findOrFail($request->item_id);

        // L'utilisateur ne peut pas démarrer une conversation avec lui-même
        if ($item->user_id == Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas démarrer une conversation pour votre propre annonce.');
        }

        // Vérifier si une conversation existe déjà
        $conversation = Conversation::where('item_id', $item->id)
            ->where('buyer_id', Auth::id())
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'item_id' => $item->id,
                'buyer_id' => Auth::id(),
                'seller_id' => $item->user_id,
            ]);
        }

        return redirect()->route('conversations.show', $conversation);
    }
}
