# Fiche Technique : US-ANN-3 (Réorganiser la galerie)

## 1. Objectif
Permettre au vendeur de réorganiser par glisser-déposer (desktop) ou via des boutons (mobile) les images de sa galerie. L'ordre défini sera l'ordre d'affichage sur la page de l'annonce.

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

---
## 4. Implémentation Front-End (Blade & JS)

- **JavaScript (Galerie) :**
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
