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

---
### Epic 18: Amélioration du Cycle de Vie des Commandes
*Rendre le processus de transaction post-paiement plus robuste, clair et sécurisé pour le vendeur et l'acheteur.*

- **US-TRS-4: Saisie du code de retrait par le vendeur**
  - **En tant que** vendeur, **Je veux** un champ pour saisir le code de retrait fourni par l'acheteur sur la page de la transaction, **Afin de** confirmer la remise en main propre de l'article.
  - **Critères d'acceptation :**
    - Sur la page de détail d'une transaction éligible à la remise en main propre (statut `payment_received`), un formulaire avec un champ de saisie pour le `pickup_code` et un bouton "Confirmer la remise" est visible pour le vendeur.
    - L'acheteur ne voit pas ce formulaire.

- **US-TRS-5: Finalisation de la transaction par code de retrait**
  - **En tant que** système, **Je veux** vérifier le code de retrait soumis par le vendeur et, s'il est correct, finaliser la transaction, **Afin de** garantir un paiement immédiat et sécurisé au vendeur.
  - **Critères d'acceptation :**
    - La soumission du formulaire `US-TRS-4` déclenche une action backend.
    - Le backend vérifie si le code fourni correspond au `pickup_code` de la transaction.
    - Si le code est correct :
        - Le statut de la transaction passe à `completed`.
        - Les fonds sont immédiatement transférés du séquestre au portefeuille du vendeur.
        - Un message de succès est affiché.
    - Si le code est incorrect, un message d'erreur est affiché au vendeur.

- **US-TRS-6: Affichage du code de retrait pour l'acheteur**
  - **En tant qu'** acheteur, **Je veux** voir clairement mon "Code de Retrait" sur la page de détail de ma commande, **Afin de** pouvoir le présenter au vendeur.
  - **Critères d'acceptation :**
    - Sur la page de détail de la transaction, si la remise en main propre est choisie, une section bien visible affiche le `pickup_code`.
    - Un texte explicatif indique à l'acheteur qu'il doit communiquer ce code au vendeur uniquement au moment de l'échange.
    - Le vendeur ne voit pas ce code sur son interface.

- **US-TRS-7: Introduire le statut de transaction "Livré"**
  - **En tant que** système, **Je veux** un nouveau statut de transaction `delivered`, **Afin de** marquer qu'un colis a été physiquement livré et initier la fenêtre de confirmation.
  - **Critères d'acceptation :**
    - Une nouvelle valeur `delivered` est ajoutée à l'énumération `TransactionStatus`.
    - Une action (potentiellement un webhook de Sendcloud ou une action manuelle "Marquer comme livré") permet de faire passer le statut d'une transaction de `in_transit` à `delivered`.
    - Un champ `delivered_at` (timestamp) est ajouté à la table `transactions` pour enregistrer ce moment.

- **US-TRS-8: Fenêtre de confirmation pour l'acheteur après livraison**
  - **En tant qu'** acheteur, **Je veux** être notifié que mon colis est livré et avoir une période de 72h pour agir, **Afin de** pouvoir confirmer la réception ou signaler un problème.
  - **Critères d'acceptation :**
    - Lorsque le statut passe à `delivered`, l'interface utilisateur pour l'acheteur sur la page de la transaction affiche un message clair : "Votre colis a été livré. Veuillez confirmer la réception sous 72h. Passé ce délai, la transaction sera automatiquement finalisée."
    - Les boutons "Confirmer la réception" et "Ouvrir un litige" sont mis en évidence.

- **US-TRS-9: Finalisation automatique de la transaction après livraison**
  - **En tant que** système, **Je veux** automatiquement finaliser les transactions et payer les vendeurs si 72h se sont écoulées depuis la livraison sans action de l'acheteur, **Afin de** ne pas bloquer indéfiniment le paiement du vendeur.
  - **Critères d'acceptation :**
    - Une tâche planifiée (scheduled job) s'exécute régulièrement (ex: toutes les heures).
    - La tâche recherche les transactions dont le statut est `delivered` et dont le `delivered_at` date de plus de 72 heures.
    - Pour chaque transaction trouvée, le statut est mis à jour à `completed`, et les fonds sont transférés au portefeuille du vendeur.

- **US-TRS-10: Génération de l'étiquette d'expédition via Sendcloud**
  - **En tant que** vendeur, **Je veux** pouvoir générer et télécharger une étiquette d'expédition depuis la page de la transaction, **Afin de** pouvoir envoyer mes articles facilement.
  - **Critères d'acceptation :**
    - Sur une transaction payée (`payment_received`) nécessitant une livraison, un bouton "Générer l'étiquette" est visible.
    - Le clic sur ce bouton appelle le `SendcloudService` pour créer un colis via l'API.
    - En cas de succès, la transaction est mise à jour avec l'ID du colis Sendcloud, le numéro de suivi, et le statut passe à `shipping_initiated`.
    - L'interface affiche un lien "Télécharger l'étiquette" qui pointe vers l'URL fournie par Sendcloud.

- **US-TRS-11: Traitement des webhooks Sendcloud pour le suivi automatique**
  - **En tant que** système, **Je veux** recevoir et traiter les webhooks de Sendcloud pour mettre à jour le statut des livraisons, **Afin d'**informer en temps réel le vendeur et l'acheteur.
  - **Critères d'acceptation :**
    - Un endpoint `POST /webhooks/sendcloud` est configuré et sécurisé par la vérification de la signature HMAC.
    - Le webhook `parcel_status_changed` est traité.
    - Le statut du colis reçu de Sendcloud est mappé à un statut interne de la transaction (ex: `shipped`, `in_transit`, `delivered`).
    - La transaction correspondante est mise à jour en base de données avec le nouveau statut.

- **US-TRS-12: Notification de livraison à l'acheteur**
  - **En tant qu'** acheteur, **Je veux** recevoir une notification (e-mail) lorsque mon colis est marqué comme "Livré", **Afin d'**être informé rapidement et de pouvoir confirmer la réception.
  - **Critères d'acceptation :**
    - Quand le statut d'une transaction passe à `delivered` (via le webhook Sendcloud), un événement est déclenché.
    - Cet événement met en file d'attente l'envoi d'un e-mail à l'acheteur de la transaction.
    - L'e-mail informe l'acheteur de la livraison et contient un lien direct vers la page de la transaction pour "Confirmer la réception" ou "Signaler un problème".
