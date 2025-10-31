<?php

namespace App\Http\Controllers;

use App\Models\ItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ItemImageController extends Controller
{
    use AuthorizesRequests;

    /**
     * Supprime une image d'annonce.
     *
     * @param  \App\Models\ItemImage  $itemImage
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ItemImage $itemImage)
    {
        // Utiliser la policy de l'item parent pour vérifier l'autorisation
        $this->authorize('update', $itemImage->item);

        // Supprimer le fichier physique
        Storage::disk('public')->delete($itemImage->path);

        // Supprimer l'enregistrement de la base de données
        $itemImage->delete();

        return response()->json(['success' => true, 'message' => 'Image supprimée avec succès.']);
    }
}
