# Feuille de Route Stratégique du Projet Pifpaf

## 1. Introduction
Ce document présente la stratégie de développement du projet Pifpaf. L'objectif est de construire une marketplace de haute qualité de manière itérative, en organisant le travail autour de **Sprints thématiques** conçus pour un développement parallèle par **trois agents IA (Jules 1, Jules 2, Jules 3)**.

La priorisation et l'organisation des tâches sont basées sur un **arbre de dépendances technologiques** qui garantit que les fonctionnalités sont développées dans un ordre logique, tout en maximisant le travail simultané.

## 2. L'Arbre Technologique & Les Lignes de Développement
Notre stratégie est visualisée par l'arbre ci-dessous. Il montre les dépendances entre les fonctionnalités et comment elles sont réparties entre les trois agents à travers les Sprints.

```mermaid
graph LR
    %% === Fondations (Acquis) ===
    subgraph " "
        direction LR
        subgraph Fondations
            direction TB
            F_Auth["Auth & Profils"]
            F_Annonces["CRUD Annonces"]
            F_Achat["Parcours Achat Simple"]
            F_Paiement["Paiement & Portefeuille"]
            F_Logistique["Logistique de base"]
        end
    end

    %% === Sprint 3 ===
    subgraph Sprint 3: Vendeur Augmenté
        direction TB
        subgraph Jules 1: Galerie
            S3_ANN1["US-ANN-1: Uploader images"] --> S3_ANN2["US-ANN-2: Définir principale"] --> S3_ANN3["US-ANN-3: Gérer galerie"]
        end
        subgraph Jules 2: IA MVP
            S3_IA1["US-IA-1: Analyse IA"] --> S3_IA2["US-IA-2: Valider suggestions"]
        end
        subgraph Jules 3: Catalogue
            S3_ANN5["US-ANN-5: Dépublier"] --> S3_ANN6["US-ANN-6: Republier"]
        end
    end

    %% === Sprint 4 ===
    subgraph Sprint 4: Logistique Avancée
        direction TB
        subgraph Jules 1: Config Vendeur
            S4_LOG1["US-LOG-1: Gérer adresses retrait"] --> S4_LOG2["US-LOG-2: Activer options / annonce"]
        end
        subgraph Jules 2: Parcours Acheteur
            S4_LOG3["US-LOG-3: Voir modes livraison"] --> S4_LOG4["US-LOG-4: Choisir mode à l'offre"] --> S4_LOG5["US-LOG-5: Gérer adresses livraison"]
        end
        subgraph Jules 3: Recherche
            S4_TRS2["US-TRS-2: Voir identité acheteur"]
            S4_LOG8["US-LOG-8: Filtrer par distance"]
        end
    end

    %% === Sprint 5 ===
    subgraph Sprint 5: Finalisation & Historique
        direction TB
        subgraph Jules 1: Post-Vente
            S5_LOG6["US-LOG-6: Ajouter code suivi"] --> S5_LOG7["US-LOG-7: Voir code suivi"]
        end
        subgraph Jules 2: Historique
            S5_HIS1["US-HIS-1: Page Mes Achats/Ventes"] --> S5_HIS3["US-HIS-3: Détail transaction"]
        end
        subgraph Jules 3: Portefeuille
             S5_TRS1["US-TRS-1: Payer avec solde"]
             S5_HIS4["US-HIS-4: Historique portefeuille"]
        end
    end

    %% === Sprint 6 ===
    subgraph Sprint 6: Communauté & IA v2
        direction TB
        subgraph Jules 1: Confiance
            S6_COM1["US-COM-1: Noter transaction"] --> S6_COM2["US-COM-2: Voir notes profil"]
            S6_COM5["US-COM-5: Ouvrir un litige"]
        end
        subgraph Jules 2: Communication
            S6_COM3["US-COM-3: Messagerie Interne"]
        end
        subgraph Jules 3: IA de Masse
            S6_IA5["US-IA-5: IA Création en masse"]
        end
    end

    %% === Sprint 7 & 8 ===
    subgraph Sprint 7: Conformité
        S7_RGPD1["US-ADM-1: Télécharger données"] --> S7_RGPD2["US-ADM-2: Supprimer compte"]
    end
    subgraph Sprint 8: Administration
        S8_ADM10["US-ADM-10: Accès Sécurisé"] --> S8_ADM11["US-ADM-11: Dashboard"]
        S8_ADM11 --> S8_ADM12["US-ADM-12: Gestion Utilisateurs"]
        S8_ADM11 --> S8_ADM13["US-ADM-13: Gestion Annonces"]
        S8_ADM11 --> S8_ADM14["US-ADM-14: Gestion Litiges"]
    end

    %% === Dépendances Inter-Sprints ===
    F_Annonces --> S3_ANN1 & S3_IA1 & S3_ANN5
    F_Auth --> S4_LOG1 & S4_LOG5
    F_Achat --> S4_LOG4 & S5_HIS1
    F_Paiement --> S5_TRS1 & S5_HIS4
    F_Logistique --> S5_LOG6
    S4_LOG1 --> S4_LOG8
    S5_HIS3 --> S6_COM1
    S6_COM5 --> S8_ADM14
```

## 3. Déroulement des Sprints

### ✔️ Sprints 0-2 : Fondations (Terminé)
- **Objectif Atteint :** Un socle fonctionnel permettant de s'inscrire, de créer une annonce simple, de l'acheter avec un système de paiement et de portefeuille simulé.

### 🚀 Sprint 3 : Vendeur Augmenté
- **Objectif :** Enrichir l'expérience du vendeur avec des outils plus puissants.
- **Lignes de Développement :**
  - **Jules 1 :** Implémentation de la galerie d'images multi-upload.
  - **Jules 2 :** Mise en place du MVP de l'IA pour la création d'annonce.
  - **Jules 3 :** Ajout des fonctionnalités de dépublication/republication.
- **Point de Synchronisation :** À la fin du sprint, un vendeur peut créer une annonce enrichie (plusieurs images), assistée par IA, et la gérer plus finement.

### 🚀 Sprint 4 : Logistique Avancée
- **Objectif :** Mettre en place un système de livraison et de retrait sur place complet.
- **Lignes de Développement :**
  - **Jules 1 :** Développement du back-office vendeur pour configurer ses options.
  - **Jules 2 :** Intégration de ces options dans le parcours d'achat.
  - **Jules 3 :** Amélioration de la transparence et de la recherche (identité, distance).
- **Point de Synchronisation :** Une transaction peut désormais inclure une option de logistique claire, choisie par l'acheteur et configurée par le vendeur.

### 🚀 Sprint 5 : Finalisation & Historique
- **Objectif :** Apporter de la visibilité post-transaction et enrichir le portefeuille.
- **Lignes de Développement :**
  - **Jules 1 :** Implémentation du suivi de colis.
  - **Jules 2 :** Création des pages d'historique des transactions.
  - **Jules 3 :** Intégration du paiement par portefeuille et de son historique.
- **Point de Synchronisation :** L'utilisateur a une vue complète de ses transactions passées, présentes et futures.

### 🚀 Sprint 6 : Communauté & IA de Masse
- **Objectif :** Construire la confiance et la communication, et préparer l'IA à la montée en charge.
- **Lignes de Développement :**
  - **Jules 1 :** Mise en place du système de notation et de litiges.
  - **Jules 2 :** Création de la messagerie interne.
  - **Jules 3 :** Développement de la fonctionnalité IA de création en masse.
- **Point de Synchronisation :** La plateforme devient plus sociale, sécurisée et puissante.

### 🚀 Sprint 7 : Conformité
- **Objectif :** Assurer la conformité avec le RGPD.
- **Lignes de Développement :**
  - **Jules 1 & 2 :** Travail conjoint sur les fonctionnalités de portabilité et de suppression des données.
- **Point de Synchronisation :** Le projet atteint sa maturité en termes de respect des données utilisateur.

### 🚀 Sprint 8 : Administration & Modération
- **Objectif :** Fournir à l'équipe les outils internes pour gérer la plateforme.
- **Lignes de Développement :**
  - **Jules 1, 2 & 3 :** Développement conjoint de l'interface d'administration, du dashboard, et des modules de gestion (utilisateurs, annonces, litiges).
- **Point de Synchronisation :** La plateforme est entièrement administrable, garantissant sa pérennité et sa sécurité.
