# Fiche Technique : US-ANN-2 (Définir l'image principale)

## 1. Objectif
Permettre au vendeur de désigner une des images de sa galerie comme étant l'image principale de l'annonce. Cette image sera utilisée comme couverture dans les listes et les résultats de recherche.

**Dépendance :** Cette US complète `US-ANN-1`. Elle part du principe que la table `item_images` existe.

---
## 2. Modifications de la Base de Données
- Aucune modification de structure. La colonne `is_primary` (boolean) sur la table `item_images` est déjà prévue.

---
## 3. Implémentation Back-End (Laravel)

- **Route API :**
  - Créer une nouvelle route `POST` pour gérer cette action de manière asynchrone (AJAX) afin d'offrir une expérience utilisateur fluide.
  - Route : `POST /items/{item}/images/{image}/set-primary`
  - Controller : `ItemImageController@setPrimary` (un nouveau contrôleur dédié serait plus propre).

- **Controller (`ItemImageController`) :**
  - **Action `setPrimary(Item $item, ItemImage $image)` :**
    - **Autorisation :** Vérifier que l'utilisateur authentifié est bien le propriétaire de l'`item` (via une Policy).
    - **Logique :**
      1.  Démarrer une transaction de base de données pour garantir l'intégrité.
      2.  Mettre `is_primary = false` pour toutes les autres images de cet `item`.
         ```php
         $item->images()->update(['is_primary' => false]);
         ```
      3.  Mettre `is_primary = true` pour l'image sélectionnée.
         ```php
         $image->update(['is_primary' => true]);
         ```
      4.  Commit la transaction.
    - **Réponse :** Retourner une réponse JSON de succès (ex: `response()->json(['status' => 'success'])`).

---
## 4. Implémentation Front-End (Blade & JS)

- **Vues à modifier :** Le script JavaScript gérant la galerie d'images (introduit dans `US-ANN-1`).
- **JavaScript (AJAX) :**
  - Sur chaque miniature d'image dans la prévisualisation, ajouter une icône (ex: une étoile ☆).
  - L'icône de l'image actuellement principale sera pleine (★), les autres seront vides (☆).
  - Au clic sur une étoile vide :
    1.  Envoyer une requête `POST` (via `fetch` ou Axios) à l'endpoint `/items/{item}/images/{image}/set-primary`.
    2.  Ajouter le header `X-CSRF-TOKEN`.
    3.  En cas de succès :
        - Mettre à jour l'état des icônes côté client : passer l'ancienne étoile principale à vide, et la nouvelle à pleine.
        - Afficher une notification discrète de succès ("Image principale mise à jour").
    4.  En cas d'échec : Afficher un message d'erreur.

---
## 5. Tests à Rédiger

- **Test d'Intégration (Feature Test) :**
  - `tests/Feature/ItemImageManagementTest.php`
  - **`test_user_can_set_a_primary_image()` :**
    - Créer un utilisateur, un item, et 3 `ItemImage` (une principale, deux non).
    - Simuler une requête `POST` authentifiée vers l'endpoint pour désigner une deuxième image comme principale.
    - `assertDatabaseHas('item_images', ['id' => $new_primary_image->id, 'is_primary' => true])`.
    - `assertDatabaseHas('item_images', ['id' => $old_primary_image->id, 'is_primary' => false])`.
  - **`test_user_cannot_set_primary_image_for_another_users_item()` :**
    - Simuler la requête avec un autre utilisateur.
    - `assertStatus(403)` (Forbidden).

- **Test Navigateur (Dusk) :**
  - `tests/Browser/ItemEditTest.php`
  - **`test_can_change_primary_image_via_interface()` :**
    - Naviguer vers la page d'édition d'une annonce avec plusieurs images.
    - Cliquer sur l'icône "étoile" d'une image non principale.
    - `waitFor` une confirmation ou un changement de l'icône.
    - Recharger la page et vérifier que la nouvelle image principale est bien la bonne.
