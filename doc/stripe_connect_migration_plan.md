# Plan de Migration vers Stripe Connect

Ce document détaille le plan d'action pour migrer le système de paiement et de portefeuille de Pifpaf vers Stripe Connect. L'objectif principal est d'effectuer cette transition de manière incrémentale, en s'assurant que la suite de tests complète de l'application (`php artisan test` et `php artisan dusk`) passe avec succès à la fin de chaque étape.

Chaque étape est conçue pour être une Pull Request distincte, petite et gérable.

## Contexte

Le système actuel repose sur un portefeuille interne et des paiements directs via Stripe. La nouvelle architecture utilisera Stripe Connect pour que les vendeurs (utilisateurs de Pifpaf) deviennent des "comptes connectés". Les transactions seront des "Direct Charges", où le client paie directement le vendeur, et Pifpaf prélève une commission (`application_fee`).

## Le Plan de Migration

### Étape 0 : Préparation et Fondations (Non-disruptif)

Cette étape met en place les bases nécessaires sans modifier aucune logique existante.

1.  **Mise à jour des dépendances et configuration :**
    *   Vérifier et mettre à jour le SDK `stripe/stripe-php` dans `composer.json` à la dernière version.
    *   Ajouter les nouvelles clés d'API Stripe Connect (si différentes) et la clé secrète du webhook dans `.env.example` et `config/services.php`.

2.  **Extension du modèle `User` :**
    *   Créer une migration pour ajouter une colonne `stripe_account_id` (type `string`, `nullable`, `unique`) à la table `users`.
    *   Cette colonne stockera l'identifiant du compte connecté Stripe pour chaque utilisateur.
    *   *Validation : Lancer les migrations. Tous les tests existants doivent passer sans modification.*

### Étape 1 : Création et Onboarding des Comptes Connectés (Fonctionnalité additive)

Nous ajoutons la capacité pour un utilisateur de devenir un vendeur sur Stripe, en parallèle du système existant.

1.  **Création du compte connecté (Backend) :**
    *   Créer un nouveau `StripeConnectController`.
    *   Ajouter une méthode `createAccount` qui, pour l'utilisateur authentifié :
        *   Vérifie s'il n'a pas déjà un `stripe_account_id`.
        *   Appelle `stripe->accounts->create()` avec les propriétés `controller` spécifiées dans le prompt.
        *   Sauvegarde le nouvel `stripe_account_id` sur le modèle `User`.
    *   Protéger cette logique avec un test de feature.

2.  **Interface d'Onboarding (UI) :**
    *   Ajouter une nouvelle section "Portefeuille Vendeur" ou "Paiements" dans le tableau de bord de l'utilisateur.
    *   Cette UI affichera le statut de l'onboarding. Pour ce faire, elle appellera une méthode dans `StripeConnectController` qui utilise `stripe->accounts->retrieve()` pour obtenir le statut en temps réel (par exemple, `charges_enabled`).
    *   Ajouter un bouton "Activer les paiements". Ce bouton déclenchera une méthode backend qui :
        *   Crée le compte connecté s'il n'existe pas (via la méthode de l'étape 1.1).
        *   Crée un "Account Link" via `stripe->accountLinks->create()`.
        *   Redirige l'utilisateur vers l'URL de l'Account Link.
    *   Définir les routes `return_url` et `refresh_url` nécessaires pour le flux de l'Account Link.
    *   *Validation : Cette nouvelle section est additive. Les tests de l'ancien flux de paiement ne sont pas affectés. Un nouveau test Dusk peut être créé pour vérifier la présence du bouton d'onboarding.*

### Étape 2 : Synchronisation des Articles Pifpaf vers les Produits Stripe (Le lien)

Chaque article mis en vente sur Pifpaf doit exister en tant que "Produit" sur le compte Stripe du vendeur.

1.  **Extension du modèle `Item` :**
    *   Créer une migration pour ajouter `stripe_product_id` et `stripe_price_id` (type `string`, `nullable`) à la table `items`.
    *   *Validation : Lancer les migrations. Tous les tests doivent passer.*

2.  **Modification de la création/mise à jour d'article :**
    *   Dans `ItemController@store` et `ItemController@update` :
        *   Après la sauvegarde de l'article dans la base de données Pifpaf, ajouter une nouvelle logique.
        *   Vérifier si `$item->user->stripe_account_id` existe et si le compte est actif.
        *   Si c'est le cas, appeler `stripe->products->create()` en passant l'en-tête `Stripe-Account: {{CONNECTED_ACCOUNT_ID}}`.
        *   Stocker les identifiants de produit et de prix retournés dans les nouvelles colonnes de l'article.
    *   Cette logique doit être "silencieuse" : si l'appel à Stripe échoue, il faut le logger, mais ne pas bloquer la création de l'article sur Pifpaf pour ne pas interrompre l'expérience utilisateur existante.
    *   *Validation : Les tests de création d'article devront être mis à jour pour "mocker" cet appel à l'API Stripe. Le comportement pour les utilisateurs non-onboardés reste inchangé.*

### Étape 3 : Implémentation du Nouveau Flux de Paiement (En parallèle)

Nous construisons le nouveau flux de paiement sans supprimer l'ancien.

1.  **Création de la session de paiement (Backend) :**
    *   Ajouter une nouvelle méthode `redirectToCheckout` dans un `CheckoutController`.
    *   Cette méthode prendra un `Item` en paramètre.
    *   Elle appellera `stripe->checkout->sessions->create()` en utilisant :
        *   Le `stripe_price_id` de l'article.
        *   L'en-tête `Stripe-Account` de l'utilisateur vendeur.
        *   Le `payment_intent_data` avec un `application_fee_amount` pour la commission de Pifpaf.
        *   Les `success_url` et `cancel_url`.
    *   La méthode redirigera l'utilisateur vers l'URL de la session de checkout Stripe.

2.  **Bouton de paiement conditionnel (UI) :**
    *   Sur la page de détail d'un article (`items.show`) :
    *   Modifier le bouton "Acheter".
    *   Utiliser une condition Blade :
        ```blade
        @if($item->user->stripe_account_id && $item->stripe_product_id)
            // Afficher le nouveau bouton "Acheter" qui pointe vers la route de `redirectToCheckout`.
        @else
            // Afficher l'ancien bouton qui utilise le flux de paiement Pifpaf.
        @endif
        ```
    *   *Validation : Cette approche "dual-path" est la clé. Les tests Dusk existants pour le paiement d'un article d'un vendeur "legacy" doivent continuer à passer. De nouveaux tests Dusk doivent être créés pour valider le nouveau parcours pour un vendeur onboardé sur Stripe Connect.*

3.  **Gestion du succès de paiement via Webhook :**
    *   Créer une nouvelle route et un contrôleur pour gérer les webhooks Stripe.
    *   Configurer le webhook dans le dashboard Stripe pour écouter l'événement `checkout.session.completed`.
    *   Dans le contrôleur de webhook :
        *   Vérifier la signature du webhook.
        *   Récupérer l'objet session.
        *   Utiliser les métadonnées de la session (ou l'ID du client) pour retrouver l'`Item` Pifpaf correspondant.
        *   Marquer l'article comme `sold`.
        *   Créer une nouvelle `Transaction` dans la base de données Pifpaf pour refléter le paiement, en enregistrant le montant, la commission, etc.
    *   *Validation : Nécessite un test d'intégration qui simule un appel de webhook Stripe.*

### Étape 4 : Dépréciation et Nettoyage de l'Ancien Système

Une fois que le nouveau flux est stable et testé, nous pouvons commencer à démanteler l'ancien. Chaque point ci-dessous sera une PR distincte.

1.  **Suppression du chemin de paiement "legacy" :**
    *   Supprimer le bloc `@else` dans la vue `items.show`. Seul le bouton de paiement Stripe Connect subsiste.
    *   *Conséquence : Les tests Dusk pour l'ancien flux de paiement vont échouer. Ils doivent être supprimés.*

2.  **Nettoyage des contrôleurs et routes :**
    *   Supprimer les anciennes routes de paiement.
    *   Élaguer l'ancien `PaymentController` de toute la logique devenue obsolète.

3.  **Migration du portefeuille :**
    *   Analyser le modèle `WalletHistory`. S'il est entièrement remplacé par les transactions Stripe, planifier sa dépréciation.
    *   Créer un script de migration de données si nécessaire pour les soldes existants.

4.  **Nettoyage final :**
    *   Supprimer les modèles, vues, et configurations liés à l'ancien système de portefeuille.
    *   Mettre à jour la documentation du projet pour refléter la nouvelle architecture de paiement.
