# Feuille de Route Stratégique du Projet Pifpaf

## 1. Introduction
Ce document présente la stratégie de développement du projet Pifpaf. L'objectif est de construire une marketplace de haute qualité de manière itérative.

Initialement organisé en **Sprints thématiques** séquentiels, le développement a évolué vers une approche plus organique, où les fonctionnalités ont été implémentées en fonction des opportunités et des dépendances techniques plutôt qu'un calendrier strict. **Ce document a été mis à jour pour refléter l'état actuel du projet.** Il sert désormais de carte globale des fonctionnalités (achevées et restantes) plutôt que d'un plan chronologique.

La priorisation et l'organisation des tâches sont basées sur un **arbre de dépendances technologiques** qui garantit que les fonctionnalités sont développées dans un ordre logique.

## 2. L'Arbre Technologique & Les Lignes de Développement
Notre stratégie est visualisée par l'arbre ci-dessous. Il montre les dépendances entre les fonctionnalités et leur état d'avancement. **Les fonctionnalités terminées sont sur fond vert.**

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

    %% === Thème: Vendeur Augmenté ===
    subgraph "Thème: Vendeur Augmenté"
        direction TB
        subgraph " "
            S3_ANN1["US-ANN-1: Uploader images"] --> S3_ANN2["US-ANN-2: Définir principale"] --> S3_ANN3["US-ANN-3: Gérer galerie"]
            S3_IA1["US-IA-1: Analyse IA"] --> S3_IA2["US-IA-2: Valider suggestions"]
            S3_ANN5["US-ANN-5: Dépublier"] --> S3_ANN6["US-ANN-6: Republier"]
            S3_ANN7["US-ANN-7: Indiquer 'Vendu'"]
        end
    end

    %% === Thème: Logistique Avancée ===
    subgraph "Thème: Logistique Avancée"
        direction TB
        subgraph " "
            S4_LOG1["US-LOG-1: Gérer adresses retrait"] --> S4_LOG2["US-LOG-2: Activer options / annonce"]
            S4_LOG3["US-LOG-3: Voir modes livraison"] --> S4_LOG4["US-LOG-4: Choisir mode à l'offre"] --> S4_LOG5["US-LOG-5: Gérer adresses livraison"]
            S4_TRS2["US-TRS-2: Voir identité acheteur"]
            S4_LOG8["US-LOG-8: Filtrer par distance"]
        end
    end

    %% === Thème: Finalisation & Historique ===
    subgraph "Thème: Finalisation & Historique"
        direction TB
        subgraph " "
            S5_LOG6["US-LOG-6: Ajouter code suivi"] --> S5_LOG7["US-LOG-7: Voir code suivi"]
            S5_HIS1["US-HIS-1: Page Mes Achats/Ventes"] --> S5_HIS3["US-HIS-3: Détail transaction"]
            S5_TRS1["US-TRS-1: Payer avec solde"]
            S5_HIS4["US-HIS-4: Historique portefeuille"]
        end
    end

    %% === Thème: Communauté & IA v2 ===
    subgraph "Thème: Communauté & IA v2"
        direction TB
        subgraph " "
            S6_COM1["US-COM-1: Noter transaction"] --> S6_COM2["US-COM-2: Voir notes profil"]
            S6_COM5["US-COM-5: Ouvrir un litige"]
            S6_COM3["US-COM-3: Messagerie Interne"]
            S6_IA5["US-IA-5: IA Création en masse"]
        end
    end

    %% === Thème: Conformité & Admin ===
    subgraph "Thème: Conformité & Admin"
        direction TB
        S7_RGPD1["US-ADM-1: Télécharger données"] --> S7_RGPD2["US-ADM-2: Supprimer compte"]
        S8_ADM10["US-ADM-10: Accès Sécurisé"] --> S8_ADM11["US-ADM-11: Dashboard"]
        S8_ADM11 --> S8_ADM12["US-ADM-12: Gestion Utilisateurs"]
        S8_ADM11 --> S8_ADM13["US-ADM-13: Gestion Annonces"]
        S8_ADM11 --> S8_ADM14["US-ADM-14: Gestion Litiges"]
    end

    %% === Dépendances Inter-Thèmes ===
    F_Annonces --> S3_ANN1 & S3_IA1 & S3_ANN5
    F_Auth --> S4_LOG1 & S4_LOG5
    F_Achat --> S4_LOG4 & S5_HIS1
    F_Paiement --> S5_TRS1 & S5_HIS4
    F_Logistique --> S5_LOG6
    S4_LOG1 --> S4_LOG8
    S5_HIS3 --> S6_COM1
    S6_COM5 --> S8_ADM14

    %% === Styles des noeuds terminés ===
    style S3_ANN1 fill:#d4edda,stroke:#c3e6cb
    style S3_ANN2 fill:#d4edda,stroke:#c3e6cb
    style S3_ANN3 fill:#d4edda,stroke:#c3e6cb
    style S3_IA1 fill:#d4edda,stroke:#c3e6cb
    style S3_IA2 fill:#d4edda,stroke:#c3e6cb
    style S3_ANN5 fill:#d4edda,stroke:#c3e6cb
    style S3_ANN6 fill:#d4edda,stroke:#c3e6cb
    style S4_LOG1 fill:#d4edda,stroke:#c3e6cb
    style S4_LOG2 fill:#d4edda,stroke:#c3e6cb
    style S4_LOG3 fill:#d4edda,stroke:#c3e6cb
    style S4_LOG4 fill:#d4edda,stroke:#c3e6cb
    style S4_TRS2 fill:#d4edda,stroke:#c3e6cb
    style S4_LOG8 fill:#d4edda,stroke:#c3e6cb
    style S5_TRS1 fill:#d4edda,stroke:#c3e6cb
    style S5_HIS4 fill:#d4edda,stroke:#c3e6cb
    style S6_COM1 fill:#d4edda,stroke:#c3e6cb
    style S6_COM3 fill:#d4edda,stroke:#c3e6cb
    style S6_COM4 fill:#d4edda,stroke:#c3e6cb
    style S6_IA5 fill:#d4edda,stroke:#c3e6cb
    style S8_ADM10 fill:#d4edda,stroke:#c3e6cb
    style S8_ADM11 fill:#d4edda,stroke:#c3e6cb
    style S8_ADM12 fill:#d4edda,stroke:#c3e6cb
```

## 3. État d'Avancement par Thème

### ✔️ Thème 0-2 : Fondations (Terminé)
- **Statut :** Un socle fonctionnel permettant de s'inscrire, de créer une annonce simple, de l'acheter avec un système de paiement et de portefeuille simulé.

### 🗺️ Thème 3 : Vendeur Augmenté (Partiellement Terminé)
- **Statut :** La majorité des fonctionnalités sont implémentées (galerie d'images, IA MVP, gestion du catalogue).
- **Restant :** `US-ANN-7` (Indiquer qu'un article est vendu sur sa page de détail).

### 🗺️ Thème 4 : Logistique Avancée (Partiellement Terminé)
- **Statut :** Le parcours de configuration vendeur et de sélection par l'acheteur est presque complet. La recherche par distance est fonctionnelle.
- **Restant :** `US-LOG-5` (Créer l'interface de gestion des adresses de livraison pour l'acheteur).

### 🗺️ Thème 5 : Finalisation & Historique (Partiellement Terminé)
- **Statut :** Le paiement par portefeuille et son historique sont fonctionnels.
- **Restant :** `US-LOG-6` & `US-LOG-7` (Gestion du suivi de colis), `US-HIS-1` & `US-HIS-3` (Historique détaillé des transactions).

### 🗺️ Thème 6 : Communauté & IA v2 (Partiellement Terminé)
- **Statut :** La messagerie interne, le système de notation initial et l'IA multi-objets sont implémentés.
- **Restant :** `US-COM-2` (Affichage des notes sur le profil), `US-COM-5` (Gestion des litiges).

### 🗺️ Thème 7 : Conformité (Non commencé)
- **Statut :** Les fonctionnalités liées au RGPD n'ont pas encore été implémentées.
- **Restant :** `US-ADM-1` (Export des données), `US-ADM-2` (Suppression de compte).

### 🗺️ Thème 8 : Administration & Modération (Partiellement Terminé)
- **Statut :** Le socle de l'interface d'administration est en place (accès, dashboard, gestion utilisateurs).
- **Restant :** `US-ADM-13` (Gestion des annonces), `US-ADM-14` (Gestion des litiges).
