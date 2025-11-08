# User Stories for Pifpaf

## Introduction
Ce document détaille les fonctionnalités du projet Pifpaf sous forme de User Stories (US), organisées en "Epics" (grandes fonctionnalités) et décomposées en stories atomiques. Chaque story inclut des Critères d'Acceptation (CA) pour guider le développement par les agents IA.

---
## 🚀 Sprint 10: Améliorations & Corrections

### Epic 14: Améliorations UX/UI
*Polir l'interface utilisateur pour une expérience plus intuitive et agréable.*

- **US-UX-1: Corriger le design des filtres**
  - **En tant que** utilisateur, **Je veux** que les filtres sur la page boutique soient bien alignés et esthétiques, **Afin de** pouvoir les utiliser facilement.
  - **Critères d'acceptation :**
    - Les éléments du formulaire de filtre (labels, champs) sont correctement alignés.
    - Le design est responsive et s'affiche correctement sur mobile.

- **US-UX-2: Organiser le tableau de bord vendeur**
  - **En tant que** vendeur, **Je veux** que mes annonces soient triées par statut sur mon tableau de bord, **Afin de** visualiser rapidement les articles pertinents.
  - **Critères d'acceptation :**
    - Par défaut, les annonces "En ligne" sont affichées en premier.
    - Ensuite, les annonces "Hors ligne", puis "Vendu".
    - Des options de filtre permettent de n'afficher qu'un seul statut.

- **US-LOG-9: Définir une adresse par défaut**
  - **En tant que** utilisateur, **Je veux** pouvoir marquer une de mes adresses (livraison ou retrait) comme étant "par défaut", **Afin de** ne pas avoir à la sélectionner à chaque fois.
  - **Critères d'acceptation :**
    - Dans les formulaires de gestion d'adresses, une case à cocher "Définir par défaut" est présente.
    - Lors du processus de commande, l'adresse par défaut est pré-sélectionnée.

### Epic 15: Fiabilisation des Flux
*Améliorer la logique métier pour la rendre plus robuste et cohérente.*

- **US-TRS-3: Sécuriser la confirmation de réception**
  - **En tant que** vendeur, **Je veux** être certain que seul l'acheteur peut confirmer la réception d'un article, **Afin de** prévenir les abus et les erreurs.
  - **Critères d'acceptation :**
    - Le bouton "Confirmer la réception" n'est visible et actif que pour l'utilisateur qui est l'acheteur de la transaction.
    - Une policy (`TransactionPolicy`) est en place pour bloquer toute tentative non autorisée côté serveur.

- **US-WAL-1: Lier l'historique du portefeuille**
  - **En tant que** utilisateur, **Je veux** voir un lien vers la transaction correspondante depuis chaque entrée de mon historique de portefeuille, **Afin de** comprendre facilement l'origine de chaque mouvement.
  - **Critères d'acceptation :**
    - Dans la table `wallet_histories`, une colonne `transaction_id` (nullable) est ajoutée.
    - Sur la page "Mon Portefeuille", chaque ligne de l'historique liée à un achat ou une vente contient un lien vers la page de détail de la transaction.

- **US-WAL-2: Centraliser les paiements via le portefeuille**
  - **En tant que** développeur, **Je veux** refactoriser le flux de paiement pour que tous les achats par carte créditent d'abord le portefeuille avant de le débiter, **Afin de** simplifier la logique comptable et l'historique.
  - **Critères d'acceptation :**
    - Lors d'un paiement par carte, deux opérations sont enregistrées dans l'historique du portefeuille : un crédit du montant payé, suivi d'un débit pour l'achat.
    - La transaction finale enregistre bien que le paiement a été fait via le portefeuille.

### Epic 16: Corrections de Bugs
*Éliminer les bugs pour assurer le bon fonctionnement de la plateforme.*

- **US-BUG-1: Réparer la création d'envoi**
  - **En tant que** vendeur, **Je veux** que le bouton "Créer l'envoi" sur mon tableau de bord fonctionne, **Afin de** pouvoir expédier mes commandes.
  - **Critères d'acceptation :**
    - Le clic sur le bouton déclenche l'action attendue (par exemple, appel à l'API Sendcloud, affichage d'une modale, etc.).
    - Le problème (JavaScript, route, etc.) qui empêche le fonctionnement est identifié et corrigé.

- **US-BUG-2: Corriger l'affichage du menu déroulant (Issue #188)**
  - **En tant que** utilisateur, **Je veux** que le menu déroulant sur la page produit s'affiche au-dessus des autres éléments, **Afin de** pouvoir interagir avec son contenu.
  - **Critères d'acceptation :**
    - Le problème de `z-index` ou de positionnement CSS est corrigé.
    - Le menu apparaît correctement sur toutes les tailles d'écran.

- **US-BUG-3: Persistance du sélecteur de statut (Issue #189)**
  - **En tant que** vendeur, **Je veux** que les options de filtrage de statut restent visibles sur mon tableau de bord même si une liste est vide, **Afin de** pouvoir naviguer entre les statuts sans être bloqué.
  - **Critères d'acceptation :**
    - Sur la page du tableau de bord (`/dashboard`), les onglets de statut ("En ligne", "Hors ligne", "Vendu") sont toujours affichés.
    - Si une catégorie est vide, un message "Aucun article trouvé pour ce statut" s'affiche sous les onglets.
    - L'utilisateur peut cliquer sur n'importe quel onglet de statut à tout moment.

- **US-BUG-4: Image manquante au checkout (Issue #173)**
  - **En tant qu'** acheteur, **Je veux** voir l'image de l'article que je m'apprête à acheter sur la page de récapitulatif de commande, **Afin d'**être certain de mon achat.
  - **Critères d'acceptation :**
    - Sur la page `/checkout/{offer}/summary`, l'image principale de l'article est correctement affichée.
    - La requête pour charger l'image ne produit pas d'erreur 404.

- **US-BUG-5: Empêcher les paiements multiples (Issue #136)**
  - **En tant qu'** acheteur, **Je veux** que le bouton de paiement soit désactivé après l'avoir cliqué une première fois, **Afin d'**éviter d'être débité plusieurs fois par erreur.
  - **Critères d'acceptation :**
    - Lors de la soumission du formulaire de paiement Stripe, le bouton "Payer" est immédiatement désactivé.
    - Un indicateur visuel (ex: spinner) montre que le paiement est en cours de traitement.
    - L'utilisateur ne peut pas soumettre le formulaire une seconde fois.

### Epic 17: Amélioration de la gestion des adresses
*Fournir une expérience plus fiable et visuelle lors de la gestion des adresses.*

- **US-LOG-10: Vérification et visualisation des adresses (Issue #107)**
  - **En tant que** utilisateur, **Je veux** que l'adresse que je saisis soit validée et affichée sur une carte, **Afin de** m'assurer de son exactitude.
  - **Critères d'acceptation :**
    - Lors de l'ajout ou de la modification d'une adresse, un appel est fait à une API de géocodage pour valider l'adresse.
    - Si l'adresse est valide, une petite carte (ex: OpenStreetMap, Google Maps) s'affiche avec un marqueur à l'emplacement trouvé.
    - Si l'adresse est invalide ou ambiguë, un message d'erreur est affiché à l'utilisateur.
