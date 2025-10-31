# Fiche Technique : US-ANN-3 (Gérer la galerie d'images)

## 1. Objectif
Permettre au vendeur de réorganiser par glisser-déposer (desktop) ou via des boutons (mobile) les images de sa galerie, et de les supprimer. L'ordre défini sera l'ordre d'affichage sur la page de l'annonce.

**Dépendance :** Complète `US-ANN-1` et `US-ANN-2`.

---
## 2. Modifications de la Base de Données
- Aucune. La colonne `order` (integer) sur la table `item_images` est déjà prévue à cet effet.

---
## 3. Implémentation Back-End (Laravel)

- **Route API :**
  - Créer une route pour mettre à jour l'ordre des images.
  - Route : `POST /items/{item}/images/reorder`
  - Controller : `ItemImageController@reorder`
  - Créer une route pour supprimer une image.
  - Route : `DELETE /items/{item}/images/{image}`
  - Controller : `ItemImageController@destroy`

- **Controller (`ItemImageController`) :**
  - **Action `reorder(Request $request, Item $item)` :**
    - **Autorisation :** Vérifier que l'utilisateur est le propriétaire de l'item.
    - **Validation :** S'assurer que le `request->input('order')` est un tableau d'IDs d'images.
    - **Logique :**
      1.  Itérer sur le tableau d'IDs reçu.
      2.  Pour chaque ID, mettre à jour le champ `order` de l'enregistrement `ItemImage` correspondant avec son index dans le tableau.
         ```php
         foreach ($request->input('order') as $index => $imageId) {
             ItemImage::where('item_id', $item->id)->where('id', $imageId)->update(['order' => $index]);
         }
         ```
    - **Réponse :** JSON de succès.
  - **Action `destroy(Item $item, ItemImage $image)` :**
    - **Autorisation :** Vérifier la propriété.
    - **Logique :**
      1.  Supprimer le fichier physique du stockage : `Storage::disk('public')->delete($image->path);`
      2.  Supprimer l'enregistrement de la base de données : `$image->delete();`
      3.  Si l'image supprimée était la principale, désigner la nouvelle première image (`order = 0`) comme principale.
    - **Réponse :** JSON de succès.

---
## 4. Implémentation Front-End (Blade & JS)

- **JavaScript (Galerie) :**
  - **Suppression :**
    - Le bouton de suppression sur chaque miniature (introduit dans `US-ANN-1`) enverra une requête `DELETE` (via AJAX/fetch) à l'endpoint `destroy`.
    - En cas de succès, retirer la miniature du DOM.
  - **Réorganisation (Desktop) :**
    - Utiliser une librairie JavaScript légère et moderne pour le glisser-déposer, comme **SortableJS**.
    - Initialiser SortableJS sur le conteneur des miniatures.
    - À l'événement `onEnd` (lorsque l'utilisateur a fini de glisser), récupérer le nouvel ordre des images (via leurs `data-id`).
    - Envoyer ce nouvel ordre (un tableau d'IDs) à l'endpoint `reorder` via une requête `POST`.
  - **Réorganisation (Mobile) :**
    - Afficher des boutons "Monter" (↑) et "Descendre" (↓) sur chaque miniature (visibles uniquement sur les écrans tactiles via des media queries CSS).
    - Au clic, réorganiser les éléments dans le DOM, puis appeler la même fonction que pour le drag-and-drop pour synchroniser avec le back-end.

---
## 5. Tests à Rédiger

- **Test d'Intégration (Feature Test) :**
  - Dans `tests/Feature/ItemImageManagementTest.php`
  - **`test_user_can_reorder_images()` :**
    - Créer un item avec 3 images (IDs 1, 2, 3 ; ordre 0, 1, 2).
    - Envoyer une requête `POST` à `/reorder` avec le tableau `['order' => [3, 1, 2]]`.
    - `assertDatabaseHas('item_images', ['id' => 3, 'order' => 0])`, etc. pour vérifier le nouvel ordre.
  - **`test_user_can_delete_an_image()` :**
    - Créer un item avec une image.
    - Simuler une requête `DELETE` sur l'endpoint `destroy`.
    - `assertDatabaseMissing('item_images', ['id' => $image->id])`.
    - `Storage::disk('public')->assertMissing($image->path)`.

- **Test Navigateur (Dusk) :**
  - Dans `tests/Browser/ItemEditTest.php`
  - **`test_can_delete_image_via_interface()` :**
    - Simuler le clic sur l'icône de suppression.
    - `waitUntilMissing()` pour s'assurer que la miniature disparaît du DOM.
    - Recharger la page et vérifier que l'image n'est plus là.
