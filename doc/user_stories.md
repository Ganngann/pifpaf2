# User Stories for Pifpaf

## Sprint 0: Initialisation et Authentification (Terminé)

### User Story 1: Inscription des utilisateurs

**En tant que** nouvel utilisateur,
**Je veux** créer un compte sur Pifpaf,
**Afin de** pouvoir mettre en vente et acheter des articles.

**Critères d'acceptation :**
- Un utilisateur peut s'inscrire avec un nom, une adresse e-mail et un mot de passe.
- Le mot de passe est stocké de manière sécurisée (hash).
- Après l'inscription, l'utilisateur est automatiquement connecté et redirigé vers son tableau de bord.

### User Story 2: Connexion des utilisateurs

**En tant qu'** utilisateur enregistré,
**Je veux** me connecter à mon compte,
**Afin d'** accéder à mon tableau de bord et gérer mes annonces.

**Critères d'acceptation :**
- Un utilisateur peut se connecter avec son e-mail et son mot de passe.
- Des messages d'erreur clairs sont affichés en cas d'échec de connexion.
- Une option "Se souvenir de moi" est disponible.

---

## Sprint 1 : Le Vendeur au Cœur du Système

### User Story 3: Création d'une annonce (manuelle)

**En tant que** vendeur,
**Je veux** créer une annonce en remplissant un formulaire manuel,
**Afin de** mettre un article en vente sur la plateforme.

**Critères d'acceptation :**
- Un formulaire permet d'ajouter un titre, une description, une catégorie, un prix et de télécharger au moins une photo.
- L'annonce est liée au profil du vendeur.
- Une fois créée, l'annonce apparaît dans le catalogue du vendeur et sur le site.

### User Story 4: Gestion du catalogue d'articles

**En tant que** vendeur,
**Je veux** un tableau de bord pour voir toutes mes annonces,
**Afin de** gérer facilement mon inventaire.

**Critères d'acceptation :**
- Le tableau de bord liste tous les articles mis en vente par l'utilisateur.
- Chaque article affiche son statut (en ligne, vendu, etc.).
- Des liens permettent de modifier ou supprimer chaque annonce.

### User Story 5: Modification d'une annonce

**En tant que** vendeur,
**Je veux** modifier une annonce existante,
**Afin de** corriger une erreur ou mettre à jour une information (ex: prix).

**Critères d'acceptation :**
- Le vendeur peut modifier tous les champs d'une annonce (titre, description, prix, etc.).
- Les modifications sont enregistrées et visibles sur la page de l'article.

### User Story 6: Suppression d'une annonce

**En tant que** vendeur,
**Je veux** supprimer une de mes annonces,
**Afin de** retirer un article qui n'est plus à vendre.

**Critères d'acceptation :**
- Le vendeur peut supprimer une annonce depuis son tableau de bord.
- Une confirmation est demandée avant la suppression définitive.
- L'annonce n'est plus visible sur le site après suppression.

---

## Sprint 2 : L'Expérience de l'Acheteur

### User Story 7: Consultation de la page d'accueil

**En tant qu'** utilisateur (acheteur potentiel),
**Je veux** voir les derniers articles ajoutés sur la page d'accueil,
**Afin de** découvrir les nouveautés.

**Critères d'acceptation :**
- La page d'accueil affiche une grille des articles les plus récents.
- Chaque article montre sa photo principale, son titre et son prix.
- Cliquer sur un article mène à sa page de détail.

### User Story 8: Recherche d'articles et filtrage

**En tant qu'** acheteur,
**Je veux** rechercher des articles par mots-clés et les filtrer,
**Afin de** trouver rapidement ce qui m'intéresse.

**Critères d'acceptation :**
- Une barre de recherche est disponible.
- Les résultats peuvent être filtrés par catégorie et par fourchette de prix.

### User Story 9: Consultation de la page de détail d'un article

**En tant qu'** acheteur,
**Je veux** consulter la page détaillée d'un article,
**Afin d'** obtenir toutes les informations nécessaires avant de faire une offre.

**Critères d'acceptation :**
- La page affiche toutes les photos, la description complète, le prix, et le profil du vendeur.
- Un bouton "Faire une offre" ou "Acheter" est visible.

### User Story 10: Faire une offre pour un article

**En tant qu'** acheteur,
**Je veux** pouvoir faire une offre sur un article,
**Afin de** négocier le prix avec le vendeur.

**Critères d'acceptation :**
- L'acheteur peut soumettre une offre avec un prix inférieur au prix demandé.
- Le vendeur peut accepter ou refuser une offre.

### User Story 10.1: Système de Notifications (MVP)

**En tant qu'** utilisateur (vendeur ou acheteur),
**Je veux** recevoir des notifications relatives au cycle de vie d'une offre,
**Afin d'** être informé en temps réel des actions requises ou des changements de statut.

**Critères d'acceptation (Vendeur) :**
- Le vendeur reçoit une notification (sur le site et/ou par email) lorsqu'une nouvelle offre est faite sur un de ses articles.
- La notification contient un lien direct vers l'offre ou l'article.

**Critères d'acceptation (Acheteur) :**
- L'acheteur reçoit une notification lorsque son offre est acceptée ou refusée.
- Si l'offre est acceptée, la notification contient un lien pour procéder à la finalisation de l'achat.

**Critères d'acceptation (Général) :**
- Un indicateur de notifications non lues est visible dans la barre de navigation.
- Les notifications peuvent être marquées comme "lues".

---

## Sprint 3 : L'IA Simplifie la Vente

### User Story 11: Mise en ligne simplifiée via une seule photo (IA)

**En tant que** vendeur,
**Je veux** créer une annonce en téléchargeant une seule photo,
**Afin de** gagner du temps et de simplifier le processus de mise en vente.

**Critères d'acceptation :**
- Le vendeur télécharge une photo.
- L'IA analyse l'image et pré-remplit automatiquement le titre, la description, la catégorie et suggère un prix.
- L'IA isole l'objet et améliore l'image principale.

### User Story 12: Validation des suggestions de l'IA

**En tant que** vendeur,
**Je veux** vérifier et modifier les informations suggérées par l'IA,
**Afin de** m'assurer que mon annonce est parfaite avant de la publier.

**Critères d'acceptation :**
- Le vendeur est redirigé vers un formulaire pré-rempli avec les suggestions de l'IA.
- Il peut éditer tous les champs avant de valider la publication de l'annonce.

---

## Sprint 4 : Transactions et Logistique

### User Story 13: Intégration du paiement

**En tant qu'** acheteur,
**Je veux** payer de manière sécurisée pour un article,
**Afin d'** être sûr que ma transaction est protégée.

**Critères d'acceptation :**
- Le paiement est géré via un service tiers (simulation de Stripe/PayPal).
- Les fonds sont séquestrés jusqu'à la confirmation de réception.

### User Story 14: Gestion du portefeuille virtuel

**En tant qu'** utilisateur,
**Je veux** avoir un portefeuille virtuel sur mon compte,
**Afin de** suivre mes gains et gérer mes retraits.

**Critères d'acceptation :**
- Les gains des ventes sont crédités sur le portefeuille.
- L'utilisateur peut demander un virement de son portefeuille vers son compte bancaire.

### User Story 15: Gestion de la livraison et du retrait sur place

**En tant que** vendeur,
**Je veux** pouvoir proposer le retrait sur place comme option,
**Afin de** faciliter les transactions locales.

**Critères d'acceptation :**
- Le vendeur peut activer l'option "Retrait sur place" lors de la création de l'annonce.
- Un code de confirmation est généré pour sécuriser la remise en main propre.

### User Story 16: Confirmation de réception

**En tant qu'** acheteur,
**Je veux** confirmer que j'ai bien reçu l'article,
**Afin de** débloquer le paiement au vendeur.

**Critères d'acceptation :**
- Un bouton "Confirmer la réception" est disponible dans le suivi de commande.
- La confirmation déclenche le transfert des fonds séquestrés vers le portefeuille du vendeur.

---

## Sprint 5 : Confiance et Communauté

### User Story 17: Système de notation et d'avis

**En tant qu'** utilisateur,
**Je veux** noter et laisser un avis sur un autre utilisateur après une transaction,
**Afin de** construire un climat de confiance sur la plateforme.

**Critères d'acceptation :**
- Vendeurs et acheteurs peuvent se laisser une note (sur 5 étoiles) et un commentaire après une vente finalisée.
- Les notes sont visibles sur le profil public des utilisateurs.

### User Story 18: Profil public de l'utilisateur

**En tant qu'** utilisateur,
**Je veux** avoir un profil public,
**Afin de** montrer ma fiabilité et voir les articles d'un vendeur.

**Critères d'acceptation :**
- Le profil affiche le nom d'utilisateur, la note moyenne et les avis reçus.
- Il liste également tous les articles actuellement en vente par cet utilisateur.

### User Story 19: Gestion des litiges

**En tant qu'** utilisateur,
**Je veux** pouvoir ouvrir un litige en cas de problème avec une transaction,
**Afin de** trouver une solution juste.

**Critères d'acceptation :**
- Un formulaire permet de déclarer un litige (article non conforme, non reçu, etc.).
- Un système de médiation (simulé) est mis en place.

### User Story 20: Conformité RGPD

**En tant qu'** utilisateur,
**Je veux** pouvoir gérer mes données personnelles et les supprimer,
**Afin de** contrôler ma vie privée.

**Critères d'acceptation :**
- L'utilisateur peut télécharger ses données.
- L'utilisateur peut supprimer son compte, ce qui efface ses données personnelles.

---
# User Story 10.1: Notifications d'Offres

**En tant que** vendeur,
**Je veux** recevoir une notification (par email et/ou sur le site) lorsqu'un acheteur fait une nouvelle offre sur un de mes articles,
**Afin d'** être informé rapidement et de pouvoir y répondre sans délai.

**Critères d'acceptation :**
- Une notification par email est envoyée au vendeur lorsqu'une nouvelle offre est créée.
- L'email contient un lien direct vers la page de l'article concerné.
- (Optionnel) Un système de notifications "cloche" est visible dans la barre de navigation du site pour les utilisateurs connectés.
- Les notifications sont marquées comme "lues" lorsque l'utilisateur les consulte.

---

**En tant qu'** acheteur,
**Je veux** recevoir une notification lorsque mon offre est acceptée ou refusée par le vendeur,
**Afin de** connaître le statut de ma proposition et de pouvoir procéder à l'achat si elle est acceptée.

**Critères d'acceptation :**
- Une notification par email est envoyée à l'acheteur lorsque le statut de son offre change (acceptée ou refusée).
- Si l'offre est acceptée, l'email contient un lien pour finaliser l'achat.
- Les notifications apparaissent également dans l'interface du site.
### User Story 21: Messagerie Interne

**En tant qu'** utilisateur (vendeur ou acheteur),
**Je veux** pouvoir envoyer et recevoir des messages directs à d'autres utilisateurs,
**Afin de** poser des questions sur un article ou de coordonner une transaction (ex: remise en main propre).

**Critères d'acceptation :**
- Un utilisateur peut initier une conversation depuis la page d'un article ou le profil d'un autre utilisateur.
- Une interface de messagerie liste les conversations et affiche les messages.
- Un indicateur de messages non lus est visible.
- (Optionnel) Les notifications par email sont envoyées pour les nouveaux messages.

---
