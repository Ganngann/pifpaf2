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
     * @param  \App\Models\ItemImage  $itemImage
     * @return \Illuminate\Http\JsonResponse
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
            ItemImage::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
