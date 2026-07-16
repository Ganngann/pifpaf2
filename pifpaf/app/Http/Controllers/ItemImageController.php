<?php

namespace App\Http\Controllers;

use App\Models\ItemImage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemImageController extends Controller
{
    use AuthorizesRequests;

    /**
     * Supprime une image d'annonce.
     *
     * @return JsonResponse
     */
    public function destroy(ItemImage $itemImage)
    {
        // Utiliser la policy de l'item parent pour vérifier l'autorisation
        $this->authorize('update', $itemImage->item);

        // Garder une référence à l'item avant de supprimer l'image
        $item = $itemImage->item;

        // Supprimer le fichier physique
        Storage::disk('public')->delete($itemImage->path);

        // Supprimer l'enregistrement de la base de données
        $itemImage->delete();

        return redirect()->route('items.edit', $item)->with('success', 'Image supprimée avec succès.');
    }

    /**
     * Définit une image comme principale.
     *
     * @return JsonResponse
     */
    public function setPrimary(ItemImage $itemImage)
    {
        $this->authorize('update', $itemImage->item);

        $item = $itemImage->item;

        // Réinitialiser l'ancienne image principale
        $item->images()->where('is_primary', true)->update(['is_primary' => false]);

        // Définir la nouvelle image principale
        $itemImage->is_primary = true;
        $itemImage->save();

        return redirect()->route('items.edit', $item)->with('success', 'Image principale mise à jour.');
    }

    /**
     * Réorganise l'ordre des images.
     *
     * @return JsonResponse
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:item_images,id',
        ]);

        $itemImage = ItemImage::find($request->ids[0]);
        $this->authorize('update', $itemImage->item);

        foreach ($request->ids as $index => $id) {
            // Mitigate IDOR: Ensure we only update images belonging to the authorized item
            ItemImage::where('id', $id)->where('item_id', $itemImage->item_id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
