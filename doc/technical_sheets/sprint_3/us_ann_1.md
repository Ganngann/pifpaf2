# Fiche Technique : US-ANN-1 (Uploader plusieurs images)

## 1. Objectif
Permettre à un vendeur d'ajouter jusqu'à 10 images à une annonce depuis le formulaire de création ou d'édition, avec une expérience utilisateur fluide sur mobile et desktop.

---
## 2. Modifications de la Base de Données

- **Action :** Créer une nouvelle table `item_images`.
- **Migration Laravel :**
  ```bash
  php artisan make:migration create_item_images_table
  ```
- **Structure de la table `item_images` :**
  - `id` (primaire, big-integer, auto-increment)
  - `item_id` (clé étrangère vers `items.id`, avec `onDelete('cascade')`)
  - `path` (string) : Stockera le chemin du fichier (ex: `item_images/1/image.jpg`).
  - `is_primary` (boolean, default `false`) : Indique si c'est l'image principale.
  - `order` (unsigned tiny integer, default `0`) : Pour la réorganisation de la galerie.
  - `timestamps` (created_at, updated_at)

---
## 3. Implémentation Back-End (Laravel)

- **Modèle Eloquent :**
  - Créer le modèle `app/Models/ItemImage.php`.
    ```bash
    php artisan make:model ItemImage
    ```
  - Dans `ItemImage.php`, définir la relation `belongsTo(Item::class)`.
  - Ajouter `item_id`, `path`, `is_primary`, `order` aux propriétés `$fillable`.

- **Modèle `Item` :**
  - Définir la relation `hasMany(ItemImage::class)`:
    ```php
    public function images()
    {
        return $this->hasMany(ItemImage::class)->orderBy('order');
    }
    ```

- **Controller (`ItemController`) :**
  - **Dans `store()` et `update()` :**
    - Après la validation et la création/mise à jour de l'objet `Item`.
    - Itérer sur les fichiers `request->file('images')`.
    - **Validation :** La validation des images (max 10, types de fichiers, taille max) doit être gérée dans un `ItemRequest`.
    - **Stockage :** Sauvegarder chaque image dans `storage/app/public/item_images/{item_id}/`. Utiliser `Storage::disk('public')->put(...)`.
    - **Base de données :** Pour chaque image, créer une entrée `ItemImage` avec le chemin, `item_id`, et l'ordre. La première image du tableau aura `is_primary = true`.

---
## 4. Implémentation Front-End (Blade & JS)

- **Vues à modifier :** `resources/views/items/create.blade.php`, `resources/views/items/edit.blade.php`.
- **Composant Blade :**
  - Remplacer l'input de fichier unique par un `<input type="file" name="images[]" multiple>`.
  - Le `[]` dans le `name` est crucial pour que Laravel reçoive un tableau de fichiers.
  - Ajouter un attribut `accept="image/png, image/jpeg"` pour guider l'utilisateur.
- **JavaScript :**
  - Un script simple (pas besoin de librairie lourde pour le MVP) sera ajouté pour gérer la prévisualisation.
  - Écouter l'événement `change` sur l'input.
  - Pour chaque fichier sélectionné, utiliser `FileReader` pour générer une miniature.
  - Créer dynamiquement les éléments `<img>` et les boutons de suppression pour chaque miniature.
  - Le bouton de suppression retirera l'image de la prévisualisation (pour l'instant, une gestion avancée de la sélection de fichiers n'est pas nécessaire, l'utilisateur devra resélectionner s'il se trompe).

---
## 5. Tests à Rédiger

- **Test d'Intégration (Feature Test) :**
  - `tests/Feature/ItemImageUploadTest.php`
  - **`test_user_can_upload_multiple_images_when_creating_item()` :**
    - Simuler une requête `POST` vers `/items` avec un tableau de 2-3 `UploadedFile::fake()->image(...)`.
    - `assertDatabaseHas('item_images', ...)` pour vérifier que les enregistrements sont créés.
    - `Storage::disk('public')->assertExists(...)` pour vérifier que les fichiers sont stockés.
  - **`test_image_upload_fails_if_more_than_10_images()` :**
    - Envoyer 11 images et `assertSessionHasErrors('images')`.
- **Test Navigateur (Dusk) :**
  - `tests/Browser/ItemCreationTest.php`
  - **`test_image_previews_are_shown_on_selection()` :**
    - Simuler l'attachement de plusieurs fichiers à l'input.
    - `assertVisible()` sur les miniatures générées par le JavaScript.
