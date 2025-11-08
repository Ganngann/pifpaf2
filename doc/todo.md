# Backlog Priorisé du Projet Pifpaf

*Ce document liste les tâches de développement restantes, priorisées pour les prochaines itérations. Il sert de source de vérité unique pour la planification.*

---
## 🎯 À Faire : Améliorations & Corrections
*Objectif : Traiter les bugs, les améliorations d'UX et les refactorings nécessaires pour polir l'application.*

- [x] **US-WAL-1:** Lier chaque transaction de l'historique du portefeuille avec la transaction correspondante.
- [x] **US-WAL-2:** Refactoriser le flux de paiement pour que tous les achats transitent par le portefeuille.
- [ ] **US-BUG-1:** Réparer le bouton "Créer l'envoi" sur le tableau de bord vendeur.
- [x] **US-UX-1:** Corriger le design des filtres sur la page boutique.
- [x] **US-BUG-2:** Corriger le problème d'affichage du menu déroulant sur la page produit.
- [ ] **US-LOG-9:** Ajouter la possibilité de définir une adresse par défaut.
- [x] **US-UX-2:** Organiser les annonces du tableau de bord vendeur par statut.
- [ ] **US-TRS-3:** S'assurer que seul l'acheteur peut confirmer la réception d'un article.
- [x] **US-BUG-3:** (Issue #189) Corriger la disparition du sélecteur de statut sur le dashboard.
- [x] **US-BUG-4:** (Issue #173) Corriger l'affichage de l'image sur la page de checkout.
- [ ] **US-BUG-5:** (Issue #136) Empêcher les paiements multiples sur un même article.
- [ ] **US-LOG-10:** (Issue #107) Mettre en place la vérification d'adresse avec une carte.
- [ ] **US-TRS-10:** Mettre en place la génération d'étiquettes d'expédition via Sendcloud.
- [ ] **US-TRS-11:** Mettre en place le traitement des webhooks Sendcloud pour le suivi automatique.
- [ ] **US-TRS-12:** Envoyer une notification par e-mail à l'acheteur lors de la livraison.

---
## 🚀 Nouvelles Fonctionnalités : Gestion Financière et Virements
*Objectif : Mettre en place le cycle de vie complet pour que les vendeurs puissent retirer leurs fonds en toute sécurité.*

- [ ] **US-W1 :** Enregistrement des informations bancaires.
- [ ] **US-W2 :** Demande de virement.
- [ ] **US-W3 :** Suivi du statut d'une demande de virement.
- [ ] **US-W4 :** Gestion et validation des demandes de virement (Admin).
- [ ] **US-W5 :** Traitement automatisé du virement.
- [ ] **US-W6 :** Notifications par email.
