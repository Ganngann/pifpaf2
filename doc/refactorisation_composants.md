# Analyse pour la Refactorisation en Composants Blade

Ce document détaille une liste exhaustive de composants Blade potentiels qui pourraient être créés pour améliorer la réutilisabilité, la maintenabilité et la clarté du code frontend de l'application.

---

## Composants d'Interface (UI) Atomiques et Moléculaires

### `ui/status-badge` [Créé]
- **Description**: Badge coloré pour afficher divers statuts.
- **Lieux d'utilisation potentiels**: `dashboard.blade.php`, `wallet/show.blade.php`, `admin/users/index.blade.php`

### `ui/item-thumbnail` [Créé]
- **Description**: Affiche la miniature d'un article avec une image de secours standardisée.
- **Lieux d'utilisation potentiels**: `dashboard.blade.php`, `transactions/purchases.blade.php`, `transactions/sales.blade.php`, `conversations/index.blade.php`

### `ui/user-profile-link` [Créé]
- **Description**: Affiche le nom d'un utilisateur comme un lien cliquable vers son profil public.
- **Lieux d'utilisation potentiels**: `items/show.blade.php`, `dashboard.blade.php`, `transactions/purchases.blade.php`, `transactions/sales.blade.php`

### `ui/empty-state` [Créé]
- **Description**: Panneau générique à afficher lorsqu'une liste est vide.
- **Lieux d'utilisation potentiels**: `dashboard.blade.php`, `profile/show.blade.php`, `conversations/index.blade.php`

### `ui/item-card` [Créé]
- **Description**: Carte publique d'un article, utilisée sur la page d'accueil et les profils.
- **Lieux d'utilisation potentiels**: `welcome.blade.php`, `profile/show.blade.php`

### `ui/offer-card` [Créé]
- **Description**: Carte unifiée pour afficher une offre (reçue ou envoyée) avec statut et actions.
- **Lieux d'utilisation potentiels**: `dashboard.blade.php`, `transactions/purchases.blade.php`

### `ui/review-card` [Créé]
- **Description**: Affiche un avis utilisateur (note, commentaire, auteur).
- **Lieux d'utilisation potentiels**: `profile/show.blade.php`

### `ui/wallet-history-card` [Créé]
- **Description**: Carte pour afficher une ligne de l'historique du portefeuille (responsive).
- **Lieux d'utilisation potentiels**: `wallet/show.blade.php`

### `ui/styled-radio-label`
- **Description**: Un `<label>` pour un input radio qui change d'apparence quand il est sélectionné.
- **Lieux d'utilisation potentiels**: `items/show.blade.php`

### `ui/search-form`
- **Description**: Un simple champ de recherche avec un bouton "Rechercher".
- **Lieux d'utilisation potentiels**: `admin/users/index.blade.php`

### `ui/dropdown` / `ui/dropdown-link`
- **Description**: Composants génériques pour créer des menus déroulants.
- **Lieux d'utilisation potentiels**: `layouts/navigation.blade.php`

### `ui/message-bubble`
- **Description**: Bulle de message pour le chat.
- **Lieux d'utilisation potentiels**: `conversations/show.blade.php`

---

## Composants du Tableau de Bord (`dashboard`, `transactions`)

### `dashboard/section-header`
- **Description**: Titre standardisé pour les sections du tableau de bord.
- **Lieux d'utilisation potentiels**: `dashboard.blade.php`

### `dashboard/item-row` & `dashboard/item-card` [Créé]
- **Description**: Représentation d'un article dans la liste "Mes annonces" (table pour desktop, carte pour mobile).
- **Lieux d'utilisation potentiels**: `dashboard.blade.php`

### `dashboard/transaction-list-item`
- **Description**: Composant unifié pour un achat ou une vente dans un historique.
- **Lieux d'utilisation potentiels**: `transactions/purchases.blade.php`, `transactions/sales.blade.php`

---
_(Les autres sections restent inchangées)_
