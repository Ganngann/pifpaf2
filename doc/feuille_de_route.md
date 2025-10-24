# Feuille de Route du Projet Pifpaf

## Introduction

Cette feuille de route découpe le développement du Proof of Concept (PoC) de Pifpaf en plusieurs sprints thématiques. L'objectif est de livrer de la valeur de manière incrémentale, en suivant une approche Test-Driven Development (TDD).

---

### ✔️ Sprint 0 : Initialisation et Authentification (Terminé)

Ce sprint initial a permis de mettre en place les fondations techniques du projet et les fonctionnalités de base de gestion des utilisateurs.

- **User Story 1 :** Inscription des utilisateurs.
- **User Story 2 :** Connexion des utilisateurs.

---

### Sprint 1 : Le Vendeur au Cœur du Système

L'objectif de ce sprint est de permettre à un utilisateur de mettre en vente un article de manière **manuelle**. Cela constitue le socle fonctionnel avant l'intégration de l'IA.

- **User Story 3 :** Création d'une annonce (formulaire manuel).
- **User Story 4 :** Gestion du catalogue d'articles (Tableau de bord Vendeur).
- **User Story 5 :** Modification d'une annonce.
- **User Story 6 :** Suppression d'une annonce.

---

### Sprint 2 : L'Expérience de l'Acheteur

Ce sprint se concentre sur les fonctionnalités permettant aux utilisateurs de trouver et d'acheter des articles.

- **User Story 7 :** Consultation de la page d'accueil (avec les derniers articles).
- **User Story 8 :** Recherche d'articles et filtrage.
- **User Story 9 :** Consultation de la page de détail d'un article.
- **User Story 10 :** Faire une offre pour un article.

---

### Sprint 3 : L'IA Simplifie la Vente

Le cœur de Pifpaf. Ce sprint intègre le moteur d'IA (Gemini 2.5 Flash) pour automatiser la création d'annonces.

- **User Story 11 :** Mise en ligne simplifiée via une seule photo (IA).
- **User Story 12 :** Validation des suggestions de l'IA par le vendeur.

---

### Sprint 4 : Transactions et Logistique

Ce sprint vise à mettre en place le système de paiement sécurisé, le portefeuille virtuel et la gestion des livraisons.

- **User Story 13 :** Intégration du paiement (simulation Stripe/PayPal).
- **User Story 14 :** Gestion du portefeuille virtuel de l'utilisateur.
- **User Story 15 :** Gestion de la livraison et du retrait sur place.
- **User Story 16 :** Confirmation de réception et déblocage des fonds.

---

### Sprint 5 : Confiance et Communauté

Ce dernier sprint ajoute les fonctionnalités sociales et de gestion des litiges pour construire une communauté de confiance.

- **User Story 17 :** Système de notation et d'avis entre utilisateurs.
- **User Story 18 :** Profil public de l'utilisateur.
- **User Story 19 :** Gestion des litiges.
- **User Story 20 :** Conformité RGPD et gestion des données.
