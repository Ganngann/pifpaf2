# Fiche Technique : US-ANN-6 (Republication d'une annonce)

## 1. Objectif
Permettre à un vendeur de remettre en vente une annonce qu'il avait précédemment dépubliée, la rendant à nouveau visible publiquement.

**Dépendance :** Suite directe de `US-ANN-5`.

---
## 2. Modifications de la Base de Données
- Aucune. La colonne et l'Enum `status` de la table `items` sont déjà en place.

---
## 3. Implémentation Back-End (Laravel)

- **Route et Controller :**
  - Créer la route symétrique à celle de la dépublication.
  - Route : `POST /my-items/{item}/publish`
  - Controller : `DashboardItemController@publish`

- **Controller (`DashboardItemController`) :**
  - **Action `publish(Item $item)` :**
    - **Autorisation :** Vérifier la propriété de l'item via une Policy.
    - **Logique :**
      ```php
      $item->status = ItemStatus::Online;
      $item->save();
      ```
    - **Redirection :** Rediriger vers le tableau de bord avec un message de succès.

---
## 4. Implémentation Front-End (Blade)

- **Vue (`resources/views/dashboard.blade.php`) :**
  - Pour chaque annonce du vendeur :
  - Si le statut est `offline`, afficher un bouton "Publier".
  - Ce bouton sera dans un formulaire pointant vers la nouvelle route :
    ```blade
    <form action="{{ route('items.publish', $item) }}" method="POST">
        @csrf
        <button type="submit">Publier</button>
    </form>
    ```

---
## 5. Tests à Rédiger

- **Test d'Intégration (Feature Test) :**
  - Dans `tests/Feature/ItemStatusTest.php`
  - **`test_user_can_publish_their_own_offline_item()` :**
    - Créer un utilisateur et un item avec le statut `offline`.
    - Simuler une requête `POST` authentifiée vers `/my-items/{item}/publish`.
    - `assertRedirect()`.
    - `assertDatabaseHas('items', ['id' => $item->id, 'status' => 'online'])`.
  - **`test_published_item_is_visible_publicly_again()` :**
    - Créer un item et le passer en `offline`, puis le repasser en `online` via la route.
    - Faire une requête `GET` sur la page d'accueil ou de recherche.
    - `assertSee($item->title)`.
  - **`test_user_cannot_publish_another_users_item()` :**
    - Créer deux utilisateurs (A et B) et un item appartenant à A avec le statut `offline`.
    - S'authentifier en tant que B et tenter de faire la requête `POST` pour publier l'item de A.
    - `assertStatus(403)` (Forbidden).
