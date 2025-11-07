# Backlog Priorisé du Projet Pifpaf

*Ce document a été entièrement mis à jour pour refléter l'état d'avancement réel du projet. Il sert de source de vérité unique pour la planification des prochaines étapes de développement.*

---
## Priorité 1 : Finaliser le Système de payement
- [x] **US-CHK-1:** Accéder au récapitulatif de commande.
- [x] **US-CHK-2:** Valider le récapitulatif de commande.
- [ ] **US-CHK-3:** Voir la confirmation de paiement.

## Priorité 2 : Finaliser le Système de Transaction et de Litiges
*Objectif : Compléter le cycle de vie d'une transaction en ajoutant les dernières touches à la logistique et en implémentant un système de gestion des litiges pour sécuriser les échanges.*

- [x] **US-LOG-7:** Permettre à l'acheteur de consulter le numéro de suivi de sa commande.
- [x] **US-COM-5:** Mettre en place un formulaire simple pour qu'un utilisateur puisse ouvrir un litige sur une transaction.
- [ ] **US-ADM-14:** Développer le module de suivi et de gestion des litiges pour l'administrateur.

---
## Priorité 2 : Assurer la Conformité RGPD
*Objectif : Mettre la plateforme en conformité avec les régulations européennes sur la protection des données, un point essentiel pour la confiance des utilisateurs et la légalité du service.*

- [x] **US-ADM-1:** Développer la fonctionnalité d'export des données personnelles pour un utilisateur.
- [x] **US-ADM-2:** Implémenter la suppression sécurisée du compte et des données utilisateur.

## Priorité 3 : Améliorer la Qualité et la Stabilité
*Objectif : Augmenter significativement la couverture de test du code pour fiabiliser les fonctionnalités critiques, réduire les régressions et faciliter la maintenance future.*

- [x] **US-TEST-1:** Améliorer la couverture de test du `PaymentController`.
- [x] **US-TEST-2:** Améliorer la couverture de test du `PickupAddressController`.
- [x] **US-TEST-3:** Améliorer la couverture de test de la logique d'IA (`AiRequestController`, `ProcessAiImage`).
- [x] **US-TEST-4:** Améliorer la couverture de test des `Policies` d'autorisation.
- [x] **US-TEST-5:** Améliorer la couverture de test des contrôleurs orphelins.
