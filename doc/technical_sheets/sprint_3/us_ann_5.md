# Fiche Technique : US-ANN-5 (Dépublication d'une annonce)

## 1. Objectif
Permettre à un vendeur de masquer temporairement une de ses annonces. L'annonce n'est plus visible publiquement mais n'est pas supprimée, conservant toutes ses données.

---
## 2. Modifications de la Base de Données

- **Action :** Ajouter une colonne `status` à la table `items`.
- **Migration Laravel :**
  ```bash
  php artisan make:migration add_status_to_items_table
  ```
- **Structure de la colonne :**
  - `$table->string('status')->default('online');`
  - Les valeurs possibles seront gérées par un Enum (PHP 8.1+) pour plus de robustesse : `online`, `offline`, `sold`.

---
## 3. Implémentation Back-End (Laravel)

- **Enum `ItemStatus` :**
  - Créer un Enum `app/Enums/ItemStatus.php`.
  - Définir les cas : `case Online = 'online';`, `case Offline = 'offline';`, `case Sold = 'sold';`

- **Modèle `Item` :**
  - Ajouter la propriété de "casting" pour l'Enum :
    ```php
    protected $casts = [
        'status' => ItemStatus::class,
    ];
    ```
  - Ajouter un "scope" pour ne récupérer que les annonces en ligne :
    ```php
    public function scopeOnline(Builder $query): void
    {
        $query->where('status', ItemStatus::Online);
    }
    ```

- **Modification des Requêtes Publiques :**
  - Partout où les annonces sont récupérées pour être affichées aux utilisateurs (page d'accueil, recherche, etc.), utiliser le nouveau scope pour exclure les annonces hors ligne.
  - Exemple : `Item::latest()->online()->paginate(20);`

- **Route et Controller :**
  - Créer une nouvelle route pour cette action.
  - Route : `POST /my-items/{item}/unpublish`
  - Controller : `DashboardItemController@unpublish` (un contrôleur dédié au tableau de bord vendeur).

- **Controller (`DashboardItemController`) :**
  - **Action `unpublish(Item $item)` :**
    - **Autorisation :** Vérifier que l'utilisateur est le propriétaire (Policy).
    - **Logique :**
      ```php
      $item->status = ItemStatus::Offline;
      $item->save();
      ```
    - **Redirection :** Rediriger vers le tableau de bord avec un message de succès.

---
## 4. Implémentation Front-End (Blade)

- **Vue (`resources/views/dashboard.blade.php`) :**
  - La requête qui récupère les annonces du vendeur ne doit **pas** utiliser le scope `online()` pour qu'il voie toutes ses annonces.
  - Pour chaque annonce, afficher son statut actuel (ex: un badge "En ligne" ou "Hors ligne").
  - Si le statut est `online`, afficher un bouton "Dépublier" qui pointe vers un petit formulaire.
    ```blade
    <form action="{{ route('items.unpublish', $item) }}" method="POST">
        @csrf
        <button type="submit" onclick="return confirm('Voulez-vous vraiment dépublier cette annonce ?')">Dépublier</button>
    </form>
    ```
  - L'alerte de confirmation JavaScript est une première sécurité simple.

---
## 5. Tests à Rédiger

- **Test d'Intégration (Feature Test) :**
  - `tests/Feature/ItemStatusTest.php`
  - **`test_user_can_unpublish_their_own_item()` :**
    - Créer un utilisateur et un item lui appartenant (`status` sera `online` par défaut).
    - Simuler une requête `POST` authentifiée vers `/my-items/{item}/unpublish`.
    - `assertRedirect()`.
    - `assertDatabaseHas('items', ['id' => $item->id, 'status' => 'offline'])`.
  - **`test_unplished_item_is_not_visible_publicly()` :**
    - Créer un item et le passer en `offline`.
    - Faire une requête `GET` sur la page d'accueil ou de recherche.
    - `assertDontSee($item->title)`.
  - **`test_unplished_item_is_still_visible_in_dashboard()` :**
    - Créer un item, le passer en `offline`.
    - Faire une requête `GET` sur le tableau de bord du propriétaire.
    - `assertSee($item->title)`.
