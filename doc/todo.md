# Pifpaf To-Do List

## ✔️ Sprint 0 : Initialisation et Authentification (Terminé)

- [x] **Mise en place de la structure du projet Laravel.**
- [x] **Implémenter la User Story 1 : Inscription des utilisateurs.**
- [x] **Implémenter la User Story 2 : Connexion des utilisateurs.**

---

## Sprint 1 : Le Vendeur au Cœur du Système (À faire)

- [x] **Implémenter la User Story 3 : Création d'une annonce (manuelle).**
    - [x] Créer la migration de la base de données pour les articles (`items`).
    - [x] Créer le formulaire de création d'annonce.
    - [x] Implémenter la logique pour sauvegarder une nouvelle annonce.
    - [x] Rédiger les tests back-end et front-end.
- [x] **Implémenter la User Story 4 : Gestion du catalogue d'articles.**
    - [x] Créer la page "Tableau de bord Vendeur".
    - [x] Afficher la liste des annonces de l'utilisateur connecté.
    - [x] Rédiger les tests back-end et front-end.
- [x] **Implémenter la User Story 5 : Modification d'une annonce.**
    - [x] Créer le formulaire de modification d'annonce.
    - [x] Implémenter la logique de mise à jour.
    - [x] Rédiger les tests back-end et front-end.
- [x] **Implémenter la User Story 6 : Suppression d'une annonce.**
    - [x] Implémenter la logique de suppression avec confirmation.
    - [x] Rédiger les tests back-end et front-end.

---

## Sprint 2 : L'Expérience de l'Acheteur (À faire)

- [x] **Implémenter la User Story 7 : Consultation de la page d'accueil.**
    - [x] Créer le contrôleur et la vue pour la page d'accueil.
    - [x] Afficher les derniers articles publiés.
    - [x] Rédiger les tests back-end et front-end.
- [x] **Implémenter la User Story 8 : Recherche d'articles et filtrage.**
    - [x] Mettre en place la barre de recherche.
    - [x] Implémenter la logique de recherche par mot-clé.
    - [x] Ajouter les filtres (catégorie, prix).
    - [x] Rédiger les tests.
- [x] **Implémenter la User Story 9 : Consultation de la page de détail d'un article.**
    - [x] Créer la page de vue détaillée pour un article.
    - [x] Afficher toutes les informations de l'article et du vendeur.
    - [x] Rédiger les tests.
- [x] **Implémenter la User Story 10 : Faire une offre pour un article.**
    - [x] Mettre en place le système d'offres.
    - [ ] Gérer les notifications pour le vendeur. (Reporté)
    - [x] Permettre au vendeur d'accepter/refuser une offre.
    - [x] Rédiger les tests.

---

## Sprint 3 : L'IA Simplifie la Vente (À faire)

- [ ] **Implémenter la User Story 11 : Mise en ligne simplifiée via une seule photo (IA).**
    - [ ] Mettre en place l'interface de téléversement de la photo.
    - [ ] Intégrer l'API de Gemini 2.5 Flash.
    - [ ] Envoyer l'image à l'IA pour analyse (catégorie, description, prix).
    - [ ] Pré-remplir le formulaire de création d'annonce avec les données de l'IA.
    - [ ] Rédiger les tests d'intégration pour l'IA.
- [ ] **Implémenter la User Story 12 : Validation des suggestions de l'IA.**
    - [ ] Permettre au vendeur de modifier les champs pré-remplis par l'IA.
    - [ ] Valider et publier l'annonce après vérification.
    - [ ] Rédiger les tests front-end pour le formulaire de validation.
