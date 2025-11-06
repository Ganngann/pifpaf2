# User Stories for Pifpaf

## Introduction
Ce document détaille les fonctionnalités du projet Pifpaf sous forme de User Stories (US), organisées en "Epics" (grandes fonctionnalités) et décomposées en stories atomiques. Chaque story inclut des Critères d'Acceptation (CA) pour guider le développement par les agents IA.

---
## ✔️ Sprint 0-2: Fondations (Terminé)
*Ces sprints ont permis de construire le socle de l'application : authentification, CRUD des annonces, parcours d'achat simple, paiement et portefeuille.*

---
## 🚀 Sprint 3: Vendeur Augmenté

### Epic 1: Galerie d'Images par Annonce
*Permettre aux vendeurs d'ajouter plusieurs photos pour rendre leurs annonces plus attractives.*

- **[TERMINÉ] US-ANN-1: Uploader et supprimer des images**
  - **En tant que** vendeur, **Je veux** pouvoir téléverser jusqu'à 10 images et supprimer des images existantes, **Afin de** gérer la galerie de mon annonce.
  - **Critères d'acceptation :**
    - Le champ de téléversement permet la sélection de multiples fichiers (PNG, JPG) jusqu'à 10.
    - Des miniatures des images sélectionnées apparaissent en prévisualisation.
    - Sur la page de modification, les images existantes sont affichées.
    - Un bouton de suppression est présent sur chaque image existante.
    - Un clic sur le bouton, après confirmation, supprime l'image de la galerie et du serveur.

- **[TERMINÉ] US-ANN-2: Définir l'image principale**
  - **En tant que** vendeur, **Je veux** pouvoir désigner une image comme étant la principale, **Afin de** choisir celle qui apparaîtra dans les résultats de recherche.
  - **Critères d'acceptation :**
    - Dans la galerie, la première image téléversée est marquée comme "Principale" par défaut.
    - L'utilisateur peut cliquer sur une icône sur une autre miniature pour la désigner comme principale.
    - Une seule image peut être principale à la fois.

- **[TERMINÉ] US-ANN-3: Réorganiser la galerie**
  - **En tant que** vendeur, **Je veux** pouvoir réorganiser les images de ma galerie, **Afin de** contrôler leur ordre d'affichage.
  - **Critères d'acceptation :**
    - Sur desktop, l'utilisateur peut glisser-déposer les miniatures pour les réorganiser.
    - Sur mobile, des flèches "monter/descendre" permettent de changer l'ordre.

### Epic 2: Création d'Annonce Assistée par IA (MVP)
*Introduction de l'IA pour simplifier la création d'une annonce unique.*

- **[TERMINÉ] US-IA-1: Analyse de l'image**
  - **En tant que** vendeur, **Je veux** un parcours de création "simplifié" où je téléverse une seule photo, **Afin que** l'IA l'analyse.
  - **Critères d'acceptation :**
    - Une nouvelle page `/items/create-with-ai` contient un champ d'upload pour une seule image.
    - **Mobile-first :** L'upload propose "Prendre une photo" ou "Choisir depuis la galerie".
    - Après l'upload, un indicateur de chargement s'affiche pendant l'appel à l'API de l'IA.
    - En cas d'échec de l'analyse, un message d'erreur est affiché à l'utilisateur.
    - En cas de succès, l'utilisateur est redirigé vers le formulaire de création manuelle.

- **[TERMINÉ] US-IA-2: Validation des suggestions**
  - **En tant que** vendeur, **Je veux** être redirigé vers un formulaire pré-rempli avec les suggestions de l'IA, **Afin de** valider ou corriger ces informations avant publication.
  - **Critères d'acceptation :**
    - Le formulaire de création (`/items/create`) est pré-rempli avec le titre, la description, la catégorie et le prix issus de la session (retour de l'IA).
    - L'image téléversée à l'étape précédente est déjà présente dans la galerie.
    - L'utilisateur peut modifier n'importe quel champ avant de soumettre le formulaire.
    - La soumission suit le processus de création d'annonce standard.

### Epic 3: Gestion du Catalogue
*Offrir plus de flexibilité aux vendeurs dans la gestion de leurs annonces.*

- **[TERMINÉ] US-ANN-5: Dépublication d'une annonce**
  - **En tant que** vendeur, **Je veux** pouvoir "dépublier" une annonce, **Afin de** la masquer temporairement du site sans la supprimer définitivement.
  - **Critères d'acceptation :**
    - Dans le tableau de bord vendeur, les annonces "En ligne" ont un bouton "Dépublier".
    - Un dialogue de confirmation apparaît au clic.
    - Après confirmation, le statut de l'annonce passe à "Hors ligne".
    - L'annonce n'est plus visible publiquement (recherche, page d'accueil) mais reste dans le tableau de bord du vendeur.

- **[TERMINÉ] US-ANN-6: Republication d'une annonce**
  - **En tant que** vendeur, **Je veux** pouvoir "republier" une annonce inactive, **Afin de** la remettre en vente facilement.
  - **Critères d'acceptation :**
    - Dans le tableau de bord vendeur, les annonces "Hors ligne" ont un bouton "Publier".
    - Au clic, le statut de l'annonce passe à "En ligne".
    - L'annonce redevient visible publiquement.

- **[TERMINÉ] US-ANN-7: Indiquer qu'un article est vendu sur sa page de détail**
  - **En tant que** utilisateur, **Je veux** voir une indication claire qu'un article est "Vendu" lorsque je consulte sa page de détail, **Afin de** ne pas tenter d'acheter un article indisponible.
  - **Critères d'acceptation :**
    - Sur la page de détail d'un article (`items.show`) dont le statut est "sold", un label "VENDU" est affiché de manière visible (par exemple, en superposition de l'image principale).
    - Sur cette même page, les boutons d'action d'achat ("Acheter", "Faire une offre") sont masqués ou désactivés.
    - Les autres informations de l'article (titre, description, vendeur, etc.) restent visibles.

---
## 🚀 Sprint 4: Logistique Avancée

### Epic 4: Options de Livraison
*Permettre aux vendeurs de définir leurs propres options de livraison et aux acheteurs de les choisir.*

- **[TERMINÉ] US-LOG-1: Gérer les adresses de retrait**
  - **En tant que** vendeur, **Je veux** gérer mes adresses de retrait dans mon profil, **Afin de** ne pas avoir à les resaisir.
  - **Critères d'acceptation :**
    - Une section "Mes Adresses de Retrait" est ajoutée au profil utilisateur.
    - L'utilisateur peut y ajouter, modifier et supprimer des adresses (nom, rue, ville, code postal).
    - L'interface est responsive et facile à utiliser sur mobile.

- **[TERMINÉ] US-LOG-2: Activer les options sur l'annonce**
  - **En tant que** vendeur, **Je veux** pouvoir activer la "Livraison à domicile" et/ou le "Retrait sur place" pour chaque annonce.
  - **Critères d'acceptation :**
    - Dans le formulaire de création d'annonce, deux interrupteurs ("toggles") sont présents.
    - Si "Retrait sur place" est activé, une liste déroulante permet de choisir parmi les adresses enregistrées (US-LOG-1).

- **[TERMINÉ] US-LOG-3: Voir les options de livraison**
  - **En tant qu'** acheteur, **Je veux** voir clairement sur une annonce les modes de récupération possibles.
  - **Critères d'acceptation :**
    - La page de l'article affiche des icônes et du texte clairs pour chaque option disponible.
    - Si le retrait est possible, la ville est affichée.

- **[TERMINÉ] US-LOG-4: Choisir le mode de livraison à l'offre**
  - **En tant qu'** acheteur, **Je veux** sélectionner mon mode de livraison au moment de faire une offre.
  - **Critères d'acceptation :**
    - Sur la page de paiement/offre, si plusieurs options de livraison sont disponibles, l'utilisateur doit en sélectionner une (boutons radio).
    - Si une seule option est disponible, elle est pré-sélectionnée.
    - Le choix impacte le calcul du prix final si des frais de livraison s'appliquent.

- **[TERMINÉ] US-LOG-5: Gérer les adresses de livraison**
  - **En tant qu'** acheteur, **Je veux** pouvoir gérer un carnet d'adresses de livraison dans mon profil.
  - **Critères d'acceptation :**
    - Une section "Mes Adresses de Livraison" est ajoutée au profil utilisateur (similaire à US-LOG-1).
    - L'utilisateur peut y ajouter, modifier et supprimer des adresses.

- **[TERMINÉ] US-TRS-2: Voir l'identité de l'acheteur**
  - **En tant que** vendeur, **Je veux** voir le pseudo de l'acheteur qui fait une offre, **Afin de** savoir à qui je m'adresse.
  - **Critères d'acceptation :**
    - Sur la page de gestion d'une offre reçue, le nom d'utilisateur public de l'acheteur est visible.
    - Ce nom est un lien vers son profil public.

- **[TERMINÉ] US-LOG-8: Filtrer par distance**
  - **En tant qu'** acheteur, **Je veux** pouvoir filtrer les annonces par distance, **Afin de** trouver des articles près de chez moi.
  - **Critères d'acceptation :**
    - Sur la page de recherche, un nouveau filtre "Distance" est disponible.
    - L'utilisateur peut entrer une adresse/ville et un rayon (ex: 10 km).
    - La recherche ne retourne que les annonces proposant le retrait sur place dans ce rayon.

---
## 🚀 Sprint 5: Finalisation & Historique

### Epic 5: Suivi de Commande
*Améliorer la transparence après la vente.*

- **[TERMINÉ] US-LOG-6: Ajouter un numéro de suivi**
  - **En tant que** vendeur, **Je veux** pouvoir ajouter un numéro de suivi à une commande expédiée.
  - **Critères d'acceptation :**
    - Dans la page "Mes Ventes", pour une commande "Expédiée", un champ permet de saisir et d'enregistrer un numéro de suivi.
    - Une fois ajouté, le statut de la commande peut passer à "En transit".

- **US-LOG-7: Consulter le suivi**
  - **En tant qu'** acheteur, **Je veux** pouvoir consulter le numéro de suivi depuis le détail de ma commande.
  - **Critères d'acceptation :**
    - Dans la page "Mes Achats", le numéro de suivi est affiché pour les commandes concernées.
    - Idéalement, le numéro est un lien qui redirige vers le site du transporteur.

### Epic 6: Historique des Transactions
*Donner aux utilisateurs une vue claire de leur activité passée.*

- **[TERMINÉ] US-HIS-1: Historique d'achats et de ventes**
  - **En tant qu'** utilisateur, **Je veux** des pages "Mes Achats" et "Mes Ventes".
  - **Critères d'acceptation :**
    - Le menu utilisateur contient des liens vers ces deux pages.
    - Chaque page liste les transactions de manière claire (photo, titre, prix, date, statut) et est paginée.
    - L'affichage est optimisé pour mobile.

- **[TERMINÉ] US-HIS-3: Détail d'une transaction**
  - **En tant qu'** utilisateur, **Je veux** cliquer sur une transaction pour en voir tous les détails.
  - **Critères d'acceptation :**
    - Chaque transaction dans l'historique est cliquable.
    - La page de détail récapitule toutes les informations : article, prix, vendeur/acheteur, dates, statut, mode de livraison, adresse utilisée, suivi.

### Epic 7: Portefeuille Amélioré
*Intégrer le portefeuille plus profondément dans l'expérience d'achat.*

- **[TERMINÉ] US-TRS-1: Payer avec le solde du portefeuille**
  - **En tant qu'** acheteur, **Je veux** pouvoir utiliser le solde de mon portefeuille pour payer mes achats.
  - **Critères d'acceptation :**
    - Sur la page de paiement, si le solde du portefeuille est > 0, une option (case à cocher) "Utiliser mon solde (X,XX €)" est visible.
    - Si cochée, le montant à payer par carte est réduit du montant du solde.
    - Si le solde couvre tout l'achat, le module de paiement par carte est masqué.

- **[TERMINÉ] US-HIS-4: Historique du portefeuille**
  - **En tant qu'** utilisateur, **Je veux** un relevé de toutes les opérations de mon portefeuille.
  - **Critères d'acceptation :**
    - Une page "Mon Portefeuille" est accessible depuis le menu utilisateur.
    - Elle affiche le solde actuel.
    - Elle liste toutes les transactions (crédit, débit, retrait) avec date, libellé et montant.

---
## 🚀 Sprint 6: Communauté & IA de Masse

### Epic 8: Confiance et Avis
*Construire une communauté fiable grâce aux évaluations.*

- **[TERMINÉ] US-COM-1: Laisser un avis**
  - **En tant qu'** utilisateur, **Je veux** pouvoir noter une transaction finalisée.
  - **Critères d'acceptation :**
    - Après qu'une transaction soit "Terminée", un bouton "Laisser un avis" apparaît dans le détail de la transaction.
    - Il ouvre un formulaire simple : une note de 1 à 5 étoiles et un champ de commentaire.
    - Un utilisateur ne peut laisser qu'un seul avis par transaction.

- **[TERMINÉ] US-COM-2: Consulter les avis**
  - **En tant qu'** utilisateur, **Je veux** consulter les avis sur le profil public des autres.
  - **Critères d'acceptation :**
    - Le profil public d'un utilisateur affiche sa note moyenne et le nombre d'avis.
    - Une section liste tous les avis reçus (note, commentaire, auteur).

- **US-COM-5: Gestion des litiges**
  - **En tant qu'** utilisateur, **Je veux** pouvoir ouvrir un litige sur une transaction.
  - **Critères d'acceptation :**
    - Un bouton "Signaler un problème" est visible sur le détail de la transaction après le paiement.
    - Il ouvre un formulaire où l'utilisateur peut décrire le problème.
    - La soumission change le statut de la transaction en "En litige" et notifie l'administrateur.

### Epic 9: Messagerie Interne
*Permettre la communication directe entre utilisateurs.*

- **[TERMINÉ] US-COM-3: Contacter un utilisateur**
  - **En tant qu'** utilisateur, **Je veux** pouvoir envoyer un message à un vendeur depuis une annonce.
  - **Critères d'acceptation :**
    - Un bouton "Contacter le vendeur" est présent sur la page de l'article.
    - S'il n'y a pas de conversation existante, une nouvelle est créée.
    - L'utilisateur est redirigé vers l'interface de messagerie.

- **[TERMINÉ] US-COM-4: Interface de messagerie**
  - **En tant qu'** utilisateur, **Je veux** une boîte de réception pour lire et répondre à mes messages.
  - **Critères d'acceptation :**
    - Une page "Messagerie" liste toutes les conversations, triées par date du dernier message.
    - Un indicateur de messages non lus est visible.
    - L'interface de chat permet d'envoyer et de voir les messages et est optimisée pour mobile.

### Epic 10: IA v2 - Création en Masse
*Faire passer l'IA à la vitesse supérieure.*

- **[TERMINÉ] US-IA-5: Traitement d'image multi-objets**
  - **En tant que** vendeur, **Je veux** uploader une photo avec plusieurs articles et que l'IA me propose de créer une annonce pour chacun.
  - **Critères d'acceptation :**
    - L'IA analyse l'image et détecte plusieurs objets.
    - La page de résultat affiche l'image avec des cadres cliquables autour de chaque objet détecté.
    - Cliquer sur un cadre lance le flux de création assistée (US-IA-2) pour cet objet spécifique.

---
## 🚀 Sprint 7: Conformité

### Epic 11: RGPD
*Garantir aux utilisateurs le contrôle de leurs données.*

- **US-ADM-1: Portabilité des données**
  - **En tant qu'** utilisateur, **Je veux** pouvoir télécharger une archive de mes données personnelles.
  - **Critères d'acceptation :**
    - Dans les paramètres du compte, un bouton "Télécharger mes données".
    - Au clic, un fichier JSON contenant les informations du compte, adresses, annonces, et transactions est généré et téléchargé.

- **US-ADM-2: Droit à l'oubli**
  - **En tant qu'** utilisateur, **Je veux** pouvoir supprimer mon compte et mes données.
  - **Critères d'acceptation :**
    - Dans les paramètres, un bouton "Supprimer mon compte".
    - Une confirmation (ex: taper le mot de passe) est requise.
    - Le compte de l'utilisateur est désactivé et ses données personnelles sont anonymisées ou supprimées.

---
## 🚀 Sprint 8: Administration & Modération

### Epic 12: Interface d'Administration (MVP)
*Fournir à l'équipe de Pifpaf les outils nécessaires pour gérer la plateforme.*

- **[TERMINÉ] US-ADM-10: Accès Sécurisé**
  - **En tant qu'** administrateur, **Je veux** une interface de connexion sécurisée et distincte.
  - **Critères d'acceptation :**
    - Un rôle "admin" est défini dans la base de données.
    - Seuls les utilisateurs avec ce rôle peuvent accéder aux routes préfixées par `/admin`.

- **[TERMINÉ] US-ADM-11: Dashboard Statistique**
  - **En tant qu'** administrateur, **Je veux** un tableau de bord avec les statistiques clés.
  - **Critères d'acceptation :**
    - La page d'accueil de l'admin affiche : nombre total d'utilisateurs, d'annonces, de transactions.
    - Des liens vers les sections de gestion sont présents.

- **[TERMINÉ] US-ADM-12: Gestion des Utilisateurs**
  - **En tant qu'** administrateur, **Je veux** pouvoir lister et bannir des utilisateurs.
  - **Critères d'acceptation :**
    - Une page liste tous les utilisateurs avec une fonction de recherche.
    - Chaque utilisateur a un bouton "Bannir" / "Réactiver" qui change son statut et l'empêche/autorise sa connexion.

- **[TERMINÉ] US-ADM-13: Gestion des Annonces**
  - **En tant qu'** administrateur, **Je veux** pouvoir lister et supprimer des annonces.
  - **Critères d'acceptation :**
    - Une page liste toutes les annonces avec une fonction de recherche.
    - Chaque annonce a un bouton "Supprimer" pour la modération.

- **US-ADM-14: Gestion des Litiges**
  - **En tant qu'** administrateur, **Je veux** pouvoir consulter et intervenir sur les litiges.
  - **Critères d'acceptation :**
    - Une page liste les transactions avec le statut "En litige".
    - L'admin peut voir les détails de la transaction et les messages échangés pour prendre une décision (rembourser, etc.).
