# Plan d'Action Final : Migration vers Stripe Connect (v3)

Ce document est la version finale et détaillée de la stratégie de migration vers Stripe Connect. Il intègre toutes les recherches documentaires et les choix d'architecture (onboarding intégré, responsabilité de la plateforme, dashboard vendeur).

L'objectif est une migration incrémentale découpée en Pull Requests (PR) atomiques, chacune laissant l'application dans un état stable avec une suite de tests entièrement fonctionnelle.

---

## Séquence des Pull Requests

### PR #1 : Fondations Techniques

**Objectif :** Préparer la base de données et la configuration.
*   **Actions :**
    1.  **Migration :** Ajouter la colonne `stripe_account_id` à la table `users`.
    2.  **Configuration :** Mettre à jour `config/services.php` et `.env.example` avec `STRIPE_WEBHOOK_SECRET`.
*   **Tests :** La suite de tests complète (`php artisan test` & `dusk`) doit passer.

---

### PR #2 : Backend pour l'Onboarding Intégré

**Objectif :** Créer les endpoints API pour initialiser le composant d'onboarding.
*   **Actions :**
    1.  Créer `StripeConnectController`.
    2.  Implémenter la logique de création de compte avec les paramètres `controller` adéquats (`losses`, `fees`, `dashboard`).
    3.  Créer un endpoint API (`GET /stripe/connect/account-session`) qui retourne le `client_secret` d'une "Account Session" Stripe.
*   **Tests :** Tests de feature (PHPUnit) pour valider la création de compte et la génération de la session.

---

### PR #3 : Frontend pour l'Onboarding Intégré

**Objectif :** Intégrer le formulaire d'onboarding Stripe dans le dashboard Pifpaf.
*   **Actions :**
    1.  Créer une vue pour l'onboarding.
    2.  Intégrer Stripe.js.
    3.  Écrire le JavaScript pour appeler l'API, récupérer le `client_secret` et initialiser le composant `account-onboarding`.
*   **Tests :** Test de navigateur (Dusk) pour vérifier l'affichage du composant.

---

### PR #4 : Synchronisation des Articles vers les Produits Stripe

**Objectif :** Associer chaque article Pifpaf à un "Produit" Stripe sur le compte du vendeur.
*   **Actions :**
    1.  **Migration :** Ajouter `stripe_product_id` et `stripe_price_id` à la table `items`.
    2.  **Logique de création/mise à jour :** Dans `ItemController`, après la création d'un article, si le vendeur est actif (`charges_enabled`), créer un produit/prix via l'API Stripe en utilisant l'en-tête `Stripe-Account`.
*   **Tests :** Mettre à jour les tests de création d'article pour "mocker" cet appel API.

---

### PR #5 : Flux de Paiement avec "Direct Charge"

**Objectif :** Mettre en place le nouveau tunnel de paiement en coexistence avec l'ancien.
*   **Actions :**
    1.  **Backend :** Créer une méthode dans `CheckoutController` qui génère une session `Stripe Checkout` incluant l'`application_fee_amount` et l'en-tête `Stripe-Account`.
    2.  **Frontend :** Sur la page de l'article, afficher le bouton du nouveau flux de paiement **seulement si `stripe_product_id` existe**. Sinon, afficher l'ancien.
*   **Tests :** Tests PHPUnit et Dusk pour le nouveau parcours, tout en conservant les tests de l'ancien parcours.

---

### PR #6 : Webhook pour la Confirmation des Commandes

**Objectif :** Gérer la confirmation de paiement de manière asynchrone.
*   **Actions :**
    1.  Créer un `StripeWebhookController` pour l'événement `checkout.session.completed`.
    2.  Implémenter la logique : vérifier la signature, marquer l'article comme vendu, créer la `Transaction`.
*   **Tests :** Test d'intégration (PHPUnit) simulant un appel webhook.

---

### PR #7 : Création du Dashboard Vendeur (Composants de Base)

**Objectif :** Fournir aux vendeurs une interface pour suivre leur activité.
*   **Actions :**
    1.  **Backend :** Créer les endpoints API pour générer des "Account Sessions" pour les composants de paiement et de virement.
    2.  **Frontend :** Créer une page "Tableau de Bord Vendeur". Y intégrer les composants Stripe `payments` et `payouts-list`.
*   **Tests :** Test de navigateur (Dusk) vérifiant la présence des composants Stripe sur la page.

---

### PR #8 : Ajout des Notifications de Conformité Vendeur

**Objectif :** Informer les vendeurs si Stripe requiert des actions de leur part.
*   **Actions :**
    1.  **Backend :** L'endpoint de session pour le dashboard vendeur doit aussi activer le composant `notification-banner`.
    2.  **Frontend :** Dans la page "Tableau de Bord Vendeur", ajouter le conteneur HTML pour le composant `notification-banner`.
*   **Tests :** Test de navigateur (Dusk) simulant un compte avec une action requise et vérifiant que la bannière s'affiche.

---

### PR #9 : Dashboard Admin pour la Gestion des Risques (MVP)

**Objectif :** Donner aux administrateurs Pifpaf un outil pour surveiller les vendeurs.
*   **Actions :**
    1.  Créer une nouvelle section "Vendeurs" dans la partie admin de l'application.
    2.  Afficher une liste des utilisateurs ayant un `stripe_account_id`.
    3.  Pour chaque utilisateur, récupérer via l'API Stripe son statut (`charges_enabled`, `payouts_enabled`) et les éventuelles restrictions.
    4.  Ajouter des liens directs vers la page du compte connecté dans le dashboard Stripe de la plateforme pour des actions manuelles.
*   **Tests :** Test de feature (PHPUnit) protégeant la nouvelle section admin et vérifiant qu'elle se charge.

---

### PR #10 et au-delà : Dépréciation de l'Ancien Système

**Objectif :** Nettoyer le code de l'ancien système de paiement.
*   **Actions (séquence de plusieurs petites PR) :**
    1.  Forcer l'onboarding pour tous les nouveaux articles (supprimer le chemin de paiement legacy).
    2.  Supprimer l'ancien `PaymentController`.
    3.  Migrer/Rembourser les soldes `WalletHistory`.
    4.  Supprimer le modèle `WalletHistory`.
    5.  Supprimer les tests devenus obsolètes.
*   **Critère de succès :** Le code est propre, maintenable, et repose à 100% sur l'architecture Stripe Connect.
