# Analyse pour la Refactorisation en Composants Blade

Ce document détaille une liste exhaustive de composants Blade potentiels qui pourraient être créés pour améliorer la réutilisabilité, la maintenabilité et la clarté du code frontend de l'application.

---

## Composants d'Interface (UI) Atomiques

### `ui/status-badge`
- **Description**: Badge coloré pour afficher divers statuts.
- **Lieux d'utilisation potentiels**:
    - `dashboard.blade.php` (Statut des annonces: En ligne, Vendu, etc.)
    - `wallet/show.blade.php` (Type d'opération: Crédit, Débit)
    - `admin/users/index.blade.php` (Statut utilisateur: Actif, Banni)

### `ui/item-thumbnail`
- **Description**: Affiche la miniature d'un article avec une image de secours standardisée.
- **Lieux d'utilisation potentiels**:
    - `dashboard.blade.php` (Dans les listes d'annonces desktop et mobile)
    - `transactions/purchases.blade.php`
    - `transactions/sales.blade.php`
    - `conversations/index.blade.php`

### `ui/user-profile-link`
- **Description**: Affiche le nom d'un utilisateur comme un lien cliquable vers son profil public.
- **Lieux d'utilisation potentiels**:
    - `items/show.blade.php` ("Vendu par : ...")
    - `dashboard.blade.php` (Dans la liste des offres reçues)
    - `transactions/purchases.blade.php`
    - `transactions/sales.blade.php`

### `ui/styled-radio-label`
- **Description**: Un `<label>` pour un input radio qui change d'apparence quand il est sélectionné.
- **Lieux d'utilisation potentiels**:
    - `items/show.blade.php` (Sélection du mode de livraison)

### `ui/empty-state`
- **Description**: Panneau générique à afficher lorsqu'une liste est vide, avec un message et un bouton d'action optionnel.
- **Lieux d'utilisation potentiels**:
    - `dashboard.blade.php` (Quand il n'y a pas d'annonces ou d'offres)
    - `profile/show.blade.php` (Quand il n'y a pas d'avis ou d'articles)
    - `conversations/index.blade.php`

### `ui/search-form`
- **Description**: Un simple champ de recherche avec un bouton "Rechercher".
- **Lieux d'utilisation potentiels**:
    - `admin/users/index.blade.php`

### `ui/dropdown` / `ui/dropdown-link`
- **Description**: Composants génériques pour créer des menus déroulants.
- **Lieux d'utilisation potentiels**:
    - `layouts/navigation.blade.php` (Menu utilisateur)

### `ui/message-bubble`
- **Description**: Bulle de message pour le chat, avec alignement et couleur variables selon l'expéditeur.
- **Lieux d'utilisation potentiels**:
    - `conversations/show.blade.php`

---

## Composants de Navigation (`layouts`)

### `layouts/nav-link` & `layouts/responsive-nav-link`
- **Description**: Liens de la barre de navigation (desktop et mobile) gérant leur état "actif".
- **Lieux d'utilisation potentiels**:
    - `layouts/navigation.blade.php`

### `layouts/wallet-balance-indicator`
- **Description**: Affiche le solde du portefeuille dans la barre de navigation.
- **Lieux d'utilisation potentiels**:
    - `layouts/navigation.blade.php`

### `layouts/responsive-user-header`
- **Description**: Affiche le nom et l'email de l'utilisateur dans le menu mobile.
- **Lieux d'utilisation potentiels**:
    - `layouts/navigation.blade.php`

---

## Composants liés aux Articles (`items`, `welcome`, `profile`)

### `item/image-gallery`
- **Description**: Galerie d'images d'un article avec image principale et miniatures.
- **Lieux d'utilisation potentiels**:
    - `items/show.blade.php`

### `item/purchase-actions`
- **Description**: Panneau d'achat complet (prix, livraison, boutons, formulaire d'offre).
- **Lieux d'utilisation potentiels**:
    - `items/show.blade.php`

### `item/search-filter`
- **Description**: Formulaire de recherche avancée pour les articles.
- **Lieux d'utilisation potentiels**:
    - `welcome.blade.php`

### `item/delivery-method-card`
- **Description**: Carte pour afficher une méthode de livraison (icône + titre + description).
- **Lieux d'utilisation potentiels**:
    - `items/show.blade.php`

### `review/card`
- **Description**: Affiche un avis utilisateur (note, commentaire, auteur).
- **Lieux d'utilisation potentiels**:
    - `profile/show.blade.php`

### `review/user-rating-summary`
- **Description**: Affiche la note moyenne et le nombre total d'avis d'un utilisateur.
- **Lieux d'utilisation potentiels**:
    - `profile/show.blade.php`

---

## Composants du Tableau de Bord (`dashboard`, `transactions`)

### `dashboard/section-header`
- **Description**: Titre standardisé pour les différentes sections du tableau de bord.
- **Lieux d'utilisation potentiels**:
    - `dashboard.blade.php`

### `dashboard/item-row` & `dashboard/item-card`
- **Description**: Représentation d'un article dans la liste "Mes annonces" (table pour desktop, carte pour mobile).
- **Lieux d'utilisation potentiels**:
    - `dashboard.blade.php`

### `dashboard/offer-received-item`
- **Description**: Affiche une offre reçue avec les boutons d'action.
- **Lieux d'utilisation potentiels**:
    - `dashboard.blade.php`

### `dashboard/offer-sent-item`
- **Description**: Affiche une offre envoyée avec son statut et ses actions.
- **Lieux d'utilisation potentiels**:
    - `dashboard.blade.php`

### `dashboard/transaction-list-item`
- **Description**: Composant unifié pour un achat ou une vente dans un historique.
- **Lieux d'utilisation potentiels**:
    - `transactions/purchases.blade.php`
    - `transactions/sales.blade.php`

---

## Composants d'Analyse IA (`ai-requests`)

### `ai/request-accordion`
- **Description**: L'élément accordéon principal pour une seule requête IA, contenant l'en-tête et le contenu déroulant.
- **Lieux d'utilisation potentiels**:
    - `ai-requests/index.blade.php`

### `ai/result-viewer`
- **Description**: Le panneau interactif complet pour un résultat d'analyse (image, boîtes, liste d'objets).
- **Lieux d'utilisation potentiels**:
    - `ai-requests/index.blade.php`

### `ai/detected-object-list-item`
- **Description**: Un élément dans la liste des objets détectés, avec sa miniature et ses actions.
- **Lieux d'utilisation potentiels**:
    - `ai-requests/index.blade.php`

### `ai/request-failed-state`
- **Description**: Le panneau affiché en cas d'échec d'une analyse, avec les options de re-tentative.
- **Lieux d'utilisation potentiels**:
    - `ai-requests/index.blade.php`

---

## Composants du Portefeuille (`wallet`) et Paiement (`payment`)

### `wallet/balance-card`
- **Description**: Grande carte affichant le solde principal.
- **Lieux d'utilisation potentiels**:
    - `wallet/show.blade.php`

### `wallet/withdrawal-form`
- **Description**: Formulaire pour effectuer une demande de virement.
- **Lieux d'utilisation potentiels**:
    - `wallet/show.blade.php`

### `wallet/history-table-row`
- **Description**: Une ligne (`<tr>`) de l'historique des transactions du portefeuille.
- **Lieux d'utilisation potentiels**:
    - `wallet/show.blade.php`

### `payment/order-summary`
- **Description**: Récapitulatif de la commande sur la page de paiement.
- **Lieux d'utilisation potentiels**:
    - `payment/create.blade.php`

### `payment/wallet-selector`
- **Description**: Logique de sélection pour l'utilisation du solde portefeuille lors d'un paiement.
- **Lieux d'utilisation potentiels**:
    - `payment/create.blade.php`

### `payment/credit-card-form`
- **Description**: Formulaire de saisie des informations de carte bancaire.
- **Lieux d'utilisation potentiels**:
    - `payment/create.blade.php`

---

## Composants de Messagerie (`conversations`)

### `conversation/list-item`
- **Description**: Un aperçu d'une conversation dans la liste de la messagerie.
- **Lieux d'utilisation potentiels**:
    - `conversations/index.blade.php`

### `conversation/header`
- **Description**: L'en-tête d'une page de conversation (image article + nom interlocuteur).
- **Lieux d'utilisation potentiels**:
    - `conversations/show.blade.php`

### `conversation/message-input-form`
- **Description**: Le formulaire pour taper et envoyer un nouveau message.
- **Lieux d'utilisation potentiels**:
    - `conversations/show.blade.php`

---

## Composants d'Administration (`admin`)

### `admin/stat-card`
- **Description**: Carte de statistique pour le tableau de bord de l'administration.
- **Lieux d'utilisation potentiels**:
    - `admin/dashboard.blade.php`

### `admin/users-table-row`
- **Description**: Une ligne (`<tr>`) de la table des utilisateurs avec les actions admin.
- **Lieux d'utilisation potentiels**:
    - `admin/users/index.blade.php`
