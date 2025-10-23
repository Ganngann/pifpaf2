# pifpaf

## 📚 Dossier de Présentation Fonctionnel : pifpaf

### Introduction et Contexte du Projet

Ce document présente les spécifications fonctionnelles du projet pifpaf, une marketplace de seconde main multi-catégories (vêtements, maison, électronique, etc.) qui se distingue par une intégration poussée de l'Intelligence Artificielle (IA) pour simplifier radicalement l'expérience de mise en vente.

- **Nom du Projet :** pifpaf
- **Objectif Principal :** Créer une plateforme de seconde main ultra-simplifiée, capable de gérer 10 utilisateurs simultanément dans le cadre d'un PoC (Proof of Concept).
- **Acteurs :** Développé intégralement par Jules.
- **Hébergement PoC :** o2switch.
- **Stack Technique (PoC) :** Laravel (Back-End), MySQL (Base de Données), Gemini 2.5 Flash (Moteur IA).

---

## 1. Moteur d'IA et Technologie (Cœur de Métier)

L'IA est le différenciateur clé de pifpaf. Elle vise à automatiser la création des annonces pour un confort maximal du vendeur. L'intégration se fera via l'API Gemini 2.5 Flash.

| ID    | Fonctionnalité IA                  | Description Détaillée                                                                                                                                                                                         |
|-------|------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| IA-01 | Segmentation d'Objets Multiples    | Capacité d'analyser une seule photo contenant plusieurs objets (ex: une pile de livres et un vêtement), de les distinguer, de les encadrer, et de les soumettre comme des articles séparés au flux de création d'annonce. |
| IA-02 | Reconnaissance et Classification   | Identifier la nature, la catégorie, la marque (si visible) et la couleur de l'objet, puis classer automatiquement l'article dans la bonne arborescence de catégories (V-02).                                    |
| IA-03 | Suggestion de Description          | Générer un titre pertinent et une description courte (1-3 phrases) à partir de l'image et de sa classification (IA-02).                                                                                      |
| IA-04 | Suggestion de Prix                 | Proposer un prix de vente basé sur l'objet identifié, la catégorie, et une estimation du marché (basé sur des données publiques simulées dans le PoC ou sur les ventes internes futures).                        |
| IA-05 | Optimisation de l'Image Principale | Isoler l'objet de l'arrière-plan des photos (retrait du fond), optimiser la luminosité et le contraste pour générer une image principale de qualité professionnelle mise en avant dans la boutique.               |

---

## 2. Fonctionnalités Vendeur (V)

| ID   | Fonctionnalité Vendeur          | Description Détaillée                                                                                                                                                                                     |
|------|---------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| V-01 | Mise en Ligne Ultra-Simplifiée  | Le vendeur sélectionne ou prend une photo. L'IA (IA-01 à IA-05) pré-remplit 80% du formulaire. Le vendeur valide ou modifie les suggestions de l'IA (prix, description, catégorie) et indique si le retrait sur place est possible. |
| V-02 | Gestion du Catalogue            | Tableau de bord pour visualiser, modifier, activer/désactiver (masquer), ou supprimer les articles mis en ligne.                                                                                             |
| V-03 | Gestion des Offres              | Possibilité de fixer le prix, d'accepter des offres d'acheteurs ou de négocier. Suivi du statut de la vente (en attente, vendu, expédié/remis).                                                               |

---

## 3. Fonctionnalités Acheteur (A)

| ID   | Fonctionnalité Acheteur       | Description Détaillée                                                                                                                     |
|------|-------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------|
| A-01 | Recherche et Filtrage Avancés | Recherche par mots-clés, catégories, prix, et état de l'article.                                                                        |
| A-02 | Détail d'Article              | Affichage de toutes les photos, description IA/manuelle, prix, état, profil du vendeur et options de retrait/livraison disponibles.       |
| A-03 | Système d'Offres              | Possibilité de faire une offre inférieure au prix affiché ou d'acheter directement.                                                       |

---

## 4. Gestion des Utilisateurs et Sécurité (U)

| ID   | Fonctionnalité Utilisateur | Description Détaillée                                                                                               |
|------|----------------------------|---------------------------------------------------------------------------------------------------------------------|
| U-01 | Inscription / Connexion    | Création de compte simple (email/mot de passe). Authentification sécurisée.                                         |
| U-02 | Profil Utilisateur         | Gestion des informations personnelles, des adresses de livraison/facturation, et des paramètres de notification.     |
| U-03 | Notation et Avis           | Système permettant aux acheteurs et vendeurs de se noter mutuellement après une transaction réussie.                  |
| U-04 | Conformité RGPD            | Gestion des consentements, droit à l'oubli et sécurisation des données personnelles.                                |

---

## 5. Fonctionnalités Paiement et Logistique (P)

| ID   | Fonctionnalité Paiement/Logistique | Description Détaillée                                                                                                                                                                 |
|------|------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| P-01 | Intégration de Paiement Sécurisé   | Utilisation d'un prestataire tiers (ex: Stripe, PayPal) pour le paiement et le séquestre des fonds jusqu'à réception de l'article.                                                    |
| P-02 | Portefeuille Virtuel               | Compte utilisateur permettant de stocker les fonds issus des ventes et de gérer les remboursements/retraits.                                                                          |
| P-03 | Gestion de la Livraison / Retrait  | Support pour l'expédition via transporteur (simulée) ou option de Retrait sur Place (Remise en main propre). Génération d'un code de confirmation pour le retrait sur place.            |
| P-04 | Frais et Commissions               | Application automatique de la commission de la plateforme sur les ventes.                                                                                                             |
| P-05 | Confirmation de Réception          | L'acheteur confirme la réception de l'article (physique ou via code de retrait) pour déclencher le versement au vendeur.                                                                 |
| P-06 | Gestion des Litiges                | Processus permettant à l'acheteur ou au vendeur d'ouvrir un litige en cas de problème (non-conformité, non-réception).                                                                  |

---

## 6. Gouvernance et Critères de Qualité (Méthodologie Jules)

Le projet pifpaf sera développé par Jules en suivant une approche de développement itératif, en mettant l'accent sur la qualité par la pratique du TDD (Test Driven Development).

### A. Processus de Développement

- **Création du Dossier doc :** Un répertoire `/doc` sera créé à la racine du projet pour stocker la documentation (ToDo List et User Stories).
- **Flux de Travail de Session (TDD) :** Pour chaque session de développement, Jules devra suivre les étapes suivantes :
    1.  **Démarrage :** Lancer le script d'initialisation de l'environnement (`setup.sh`).
    2.  **Planification :** Consulter la documentation dans `/doc` pour connaître l'état actuel du projet et la prochaine User Story (US) à traiter.
    3.  **Exécution :** Développer la fonctionnalité en cours, en s'assurant que tous les tests (Front et Back) sont rédigés et passent.
    4.  **Clôture :** Mettre à jour la documentation, les tests, et la ToDo List dans le dossier `/doc`.
- **Développement par US :** Chaque User Story (US) est développée séquentiellement.
- **Tests :** Les tests Back-End (Laravel) et Front-End (JS/outils de test) sont développés avant ou pendant la fonctionnalité.

### B. Critères d'Acceptation Non-Négociables

Pour qu'une User Story soit considérée comme **Terminée**, elle doit satisfaire les critères d'acceptation de la US, plus les quatre critères de qualité suivants :

| C.A. ID | Critère d'Acceptation        | Exigence                                                                                                      |
|---------|------------------------------|---------------------------------------------------------------------------------------------------------------|
| C.A. 1  | Fonctionnalité Opérationnelle   | La fonctionnalité demandée dans l'US est implémentée et fonctionne conformément aux spécifications.           |
| C.A. 2  | Couverture des Tests         | Des tests Front-End et Back-End couvrant la fonctionnalité développée doivent avoir été mis en place.          |
| C.A. 3  | Non-Régression (Règle d'Or)   | **TOUS** les tests (y compris ceux des US précédentes) doivent passer avec succès. L'introduction d'une nouvelle fonctionnalité ne doit jamais casser une fonctionnalité existante. |
| C.A. 4  | Documentation Visuelle       | Des captures d'écran du rendu front-end de la fonctionnalité doivent être ajoutées à la documentation de la User Story. |

---

### Conclusion

Le projet pifpaf se positionne comme une marketplace de nouvelle génération, où l'automatisation par l'IA (Gemini 2.5 Flash) est utilisée pour éliminer la friction de la mise en vente. Le développement, mené par Jules, sera encadré par une méthodologie stricte de tests pour garantir un PoC de haute qualité.
