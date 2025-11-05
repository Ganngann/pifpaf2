# Pifpaf - Marketplace de Seconde Main Assistée par IA

> [!IMPORTANT]
> **Directive pour l'Agent IA (Jules)**
> Toute intervention sur ce projet doit **impérativement** suivre la procédure définie dans le fichier `AGENTS.md`. C'est une règle non-négociable.
> **[Consulter la directive](./AGENTS.md)**

## 1. Vue d'ensemble du Projet

Pifpaf est un Proof of Concept (PoC) pour une marketplace de seconde main qui se distingue par une intégration poussée de l'Intelligence Artificielle (IA) via **Gemini 2.5 Flash**. L'objectif est de simplifier radicalement l'expérience de mise en vente en automatisant la création d'annonces à partir d'une simple photo.

- **Stack Technique :** Laravel (Back-End), MySQL (Base de Données), SQLite (Tests).
- **Hébergement PoC :** o2switch.

Pour une description détaillée des fonctionnalités, veuillez consulter le dossier `/doc`.

---

## 2. Notre Méthodologie de Développement

Ce projet est développé en suivant une méthodologie agile sur mesure, conçue pour un développement rapide et de haute qualité par une équipe d'**agents IA (Jules)**.

### A. Philosophie : Granularité et Parallélisme

- **Epics & User Stories :** Les fonctionnalités sont définies en tant qu'**Epics** (ex: "Gestion de la Logistique") puis décomposées en **User Stories (US) atomiques**. Chaque US est une tâche suffisamment petite et précise pour être traitée par un agent avec un minimum d'ambiguïté.
- **Arbre Technologique :** L'ordre de développement est dicté par un **arbre de dépendances**. Une US ne peut être commencée que si ses prérequis sont complétés. Cet arbre est la source de vérité pour la priorisation.
- **Développement Parallèle :** Le travail est réparti sur **trois lignes de développement simultanées (Jules 1, Jules 2, Jules 3)**. Chaque agent travaille sur une branche indépendante de l'arbre technologique pour maximiser la productivité.

### B. Organisation en Sprints

Le développement est rythmé par des **Sprints thématiques**. Chaque Sprint a un objectif clair et se termine par un **point de synchronisation**, où les avancées des trois lignes sont intégrées et validées collectivement.

#### Phase de Planification Technique (Pré-Sprint)
Avant le démarrage de chaque Sprint, une phase de **Planification Technique** est réalisée. Pour chaque User Story du Sprint à venir, une **Fiche Technique d'Implémentation** est rédigée.

Ces fiches décrivent la solution technique envisagée (modèles, migrations, routes, logique du contrôleur, tests à écrire, etc.) et servent de plan d'attaque détaillé pour les agents développeurs. Elles sont stockées dans le répertoire `/doc/technical_sheets/`.

**Note sur la flexibilité :** Ces fiches servent de trajectoire par défaut. L'agent développeur est tenu de les suivre, mais est autorisé à dévier si une meilleure solution émerge du contexte du code existant. Toute déviation doit être explicitement justifiée dans le rapport de fin de tâche.

### C. Qualité et Documentation (Règle d'Or)

- **Test-Driven Development (TDD) :** Chaque US doit être accompagnée de tests (Back-End et Front-End) qui valident son fonctionnement et assurent la non-régression.
- **Documentation Vivante :** La stratégie, les tâches et les objectifs sont maintenus à jour dans les documents suivants :
    - **`README.md` (ce fichier) :** Le portail d'entrée du projet.
    - **`doc/feuille_de_route.md` :** La vision stratégique, l'arbre technologique et le plan des Sprints.
    - **`doc/user_stories.md` :** Le catalogue détaillé de tous les Epics et User Stories.
    - **`doc/todo.md` :** La checklist opérationnelle pour le suivi de l'avancement.
    - **`doc/agent_mission_template.md` :** Le manuel de procédure pour le développement d'une tâche.

Cette méthodologie garantit un développement structuré, rapide et transparent, tout en capitalisant sur la capacité des agents IA à exécuter des tâches bien définies en parallèle.

---

## 3. Démarrage Rapide

Pour initialiser l'environnement de développement, exécutez le script suivant à la racine du projet :
```bash
bash setup.sh
```
Ce script installera les dépendances, configurera la base de données et préparera l'application.

---

## 4. Lancement d'une Mission de Développement

Pour lancer un agent sur une tâche, utiliser le prompt suivant :

---

**Bonjour Jules,**

Ta mission aujourd'hui est d'implémenter la **User Story : `[ID_US, ex: US-ANN-1]`**.

Pour ce faire, tu dois suivre à la lettre le manuel de procédure qui t'est destiné. **Lis-le attentivement avant de commencer :**

`doc/agent_mission_template.md`

Remplace `[ID_US]` par l'identifiant de ta mission dans toutes les étapes du manuel.

**Bon développement !**
