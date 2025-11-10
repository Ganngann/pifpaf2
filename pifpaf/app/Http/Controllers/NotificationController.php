<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Affiche les notifications de l'utilisateur.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(10);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marque une notification comme lue.
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back();
    }

    /**
     * Redirige vers le lien de la notification et la marque comme lue.
     */
    public function readAndRedirect(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['link'] ?? route('notifications.index'));
    }
}
