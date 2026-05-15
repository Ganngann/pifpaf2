# Analyse de Préparation à la Production (Production Readiness)

Ce document compile les problèmes, fonctionnalités incomplètes, et incohérences identifiés dans la base de code de Pifpaf. Il a pour but de fournir un état des lieux clair avant d'envisager un déploiement en production, en listant ce qui ne va pas, pourquoi, et quelles sont les pistes de solution.

---

## 1. Dépendance des tests (Front-End) aux services externes

### Le Problème
Plusieurs tests automatisés dans la suite Laravel Dusk (Front-End) échouent ou sont intentionnellement ignorés (`$this->markTestSkipped()`) car ils dépendent d'API externes réelles (Stripe pour les paiements, Gemini pour l'IA).
**Exemples :** `AiItemCreationDuskTest.php`, `PaymentTest.php`, `PaymentFlowTest.php`.

### Pourquoi ça pose problème
Une suite de tests doit être déterministe, rapide, et capable de s'exécuter dans n'importe quel environnement (notamment CI/CD) sans générer de "fausses" données externes (transactions parasites Stripe) ni échouer à cause de limites de taux (rate limits) d'API tierces. Des tests sautés signifient qu'une partie critique du parcours utilisateur (paiement, création par IA) n'est pas validée automatiquement lors des déploiements.

### Pistes de Solution
*   **Mocker les appels API dans Dusk :** Contrairement à PHPUnit où on peut facilement mocker avec HTTP Fake, Dusk exécute un vrai navigateur de bout en bout. La solution standard consiste à créer de fausses implémentations (Fakes) ou des services "Mock" au niveau du conteneur de dépendances (DI) qui sont injectés *uniquement* lorsque l'application tourne dans l'environnement de test Dusk.
*   **Mode "Test" dédié (Feature Flags) :** Introduire un paramètre `.env.dusk.local` spécifique qui force les contrôleurs à simuler un succès au lieu d'appeler Stripe/Gemini.
*   **Utiliser Stripe CLI (Local/CI) :** Si on veut tester le flux de bout en bout, utiliser les données de test (cartes de test fournies par Stripe) et écouter les événements via Stripe CLI localement.

---

## 2. Incohérence dans le cycle de vie des transactions (Order Lifecycle)

### Le Problème
La documentation technique (`doc/technical_sheets/order_lifecycle.md`) identifie un flux "As-Is" défaillant et propose un flux "To-Be" robuste, mais le code actuel ne reflète pas encore cette transition cible.

### Pourquoi ça pose problème
*   **Blocage des fonds (Remise en main propre) :** Actuellement, lors d'une remise en main propre, le paiement est conditionné *uniquement* par la confirmation de réception par l'acheteur (`confirmReception`). Si l'acheteur oublie, les fonds du vendeur restent bloqués, même s'il a confirmé de son côté.
*   **Absence de statut de livraison ("delivered") :** Le flux "To-Be" mentionne la nécessité d'un statut `DELIVERED` (`delivered`) et d'une fenêtre de confirmation (ex: 72h) pour l'acheteur, suivi d'une libération automatique. Le modèle `TransactionStatus` inclut bien `DELIVERED`, mais aucune logique de validation automatique (tâche planifiée/cron job) n'est implémentée pour débloquer les fonds au bout de 72h.

### Pistes de Solution
*   **Implémenter le flux "Remise en main propre" (US-TRS-5) :** Permettre au vendeur de valider la remise en main propre en saisissant le `pickup_code` généré au moment de l'achat. Cela doit faire passer la transaction directement à `completed` et payer le vendeur immédiatement.
*   **Job asynchrone pour la livraison (US-TRS-9) :** Créer un job (via Laravel Scheduler, ex: `php artisan schedule:work`) qui tourne quotidiennement. Ce job chercherait les transactions au statut `delivered` (avec un timestamp `delivered_at` qui n'existe pas encore dans la base) datant de plus de 72h, et les basculerait à `completed`.

---

## 3. Paramétrage Environnemental Insuffisant

### Le Problème
Le fichier `.env.example` liste les variables d'environnement nécessaires pour les services (Stripe, Sendcloud, AWS, Gemini, API de géocodage), mais sans fournir de mocks ou de valeurs par défaut pour les environnements de développement.

### Pourquoi ça pose problème
Lorsqu'un nouveau développeur (ou un agent) installe le projet, ou lors du lancement de la CI/CD, l'application est "cassée" par défaut sur ces fonctionnalités si des clés API réelles ne sont pas explicitement configurées. Cela ralentit le développement et complique l'intégration.

### Pistes de Solution
*   **Null Objects / Dummies :** Dans la configuration des services (`config/services.php` ou `AppServiceProvider`), mettre en place des logiques conditionnelles. Si une clé API n'est pas trouvée dans un environnement `local` ou `testing`, lier une classe "Dummy" au conteneur de services qui retourne des données fictives au lieu de lancer une exception.
*   **Documentation .env :** Compléter le `README.md` avec des instructions explicites sur comment obtenir les clés de test pour ces services, ou comment activer le mode "développement isolé".

---

## 4. Fonctionnalités de base manquantes (Feuille de route)

### Le Problème
La lecture de `doc/feuille_de_route.md` montre que des blocs entiers nécessaires au bon fonctionnement de la plateforme en production ne sont pas commencés.

### Pourquoi ça pose problème
Une plateforme de "seconde main" qui permet de générer des fonds (portefeuille) ne peut pas être mise en production sans un système pour retirer ces fonds.

### Pistes de Solution
*   **Prioriser le Thème 11 (Gestion Financière & Virements) :** C'est un prérequis légal et fonctionnel absolu avant toute mise en production. Les utilisateurs doivent pouvoir demander le virement de leur solde vers leur compte bancaire.
*   **Terminer le Thème 10 (US-LOG-10) :** La vérification des adresses par géocodage (#107) est essentielle pour l'intégration avec Sendcloud, sous peine de générer des erreurs d'étiquettes d'expédition au moment de la livraison.
