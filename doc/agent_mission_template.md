# Modèle de Mission pour Agent de Développement Jules

**OBJECTIF :** Implémenter la User Story : **`[ID_US]`**

---
## PHASE 0 : PRÉPARATION SYSTÈME

Ta première action est de garantir un environnement de travail sain et à jour.

1.  **Exécute le script de configuration :**
    ```bash
    bash setup.sh
    ```
2.  **Vérifie la sortie :** Assure-toi que le script se termine sans erreur fatale.

---
## PHASE 1 : ASSIMILATION DU CONTEXTE

Ton but est de comprendre parfaitement ta mission.

1.  **Charge la User Story :**
    *   Utilise `grep` pour trouver la section de ta mission dans `doc/user_stories.md`.
    *   Exemple : `grep -A 15 "\[ID_US\]:" doc/user_stories.md`
    *   Lis et comprends la description et les critères d'acceptation.

2.  **Charge la Fiche Technique :**
    *   Le nom du fichier est basé sur l'ID (ex: `us_ann_1.md`). Trouve-le dans `doc/technical_sheets/`.
    *   Exemple : `find doc/technical_sheets -name "\[ID_US\].md"`
    *   Lis et comprends le plan d'implémentation technique proposé.

---
## PHASE 2 : ANALYSE STRATÉGIQUE DU CODE EXISTANT (ACTION LA PLUS IMPORTANTE)

Ta mission n'existe pas dans le vide. Ton code doit s'intégrer parfaitement. **La fiche technique est une hypothèse que tu dois valider contre la réalité du code.**

1.  **Cartographie du Terrain (en suivant le flux Laravel) :**
    *   **Point d'Entrée (Routes) :** Commence par analyser les fichiers de routes (`pifpaf/routes/web.php` et `pifpaf/routes/api.php`). Identifie les routes et les contrôleurs qui sont liés à ta mission.
    *   **Logique Applicative (Controller) :** Ouvre le(s) contrôleur(s) que tu as identifié(s). Étudie les méthodes existantes. C'est ici que la logique principale est orchestrée.
    *   **Données (Model) :** Depuis le contrôleur, identifie les modèles Eloquent utilisés. Ouvre ces fichiers (`pifpaf/app/Models/`). Analyse leurs propriétés (`$fillable`, `$casts`) et, surtout, leurs **relations**.
    *   **Présentation (View) :** Si ta mission a un impact sur l'interface, identifie les vues Blade retournées par le contrôleur (`pifpaf/resources/views/`). Analyse leur structure et les composants qu'elles utilisent.
    *   **Structure de la Base de Données (Migration) :** Trouve la migration correspondante au modèle que tu analyses dans `pifpaf/database/migrations/` pour comprendre la structure exacte de la table.

2.  **Analyse d'Impact et Raffinement du Plan :**
    *   Confronte maintenant la solution de la fiche technique avec tout ce que tu viens d'apprendre.
    *   Pose-toi les questions suivantes :
        *   *Puis-je réutiliser une relation, un scope de modèle ou une méthode de contrôleur existante ?*
        *   *La fiche suggère-t-elle de créer quelque chose qui existe déjà sous une autre forme ?*
        *   *Mon changement dans cette vue ne va-t-il pas casser le style ou un composant AlpineJS existant ?*

3.  **Formalisation de ton Plan d'Action Final :**
    *   Sur la base de cette analyse approfondie, finalise ton plan.
    *   **Rédige un bref plan d'action en 3 à 5 points.** Si tu dévies de la fiche technique, c'est ici que tu dois le mentionner pour la première fois.

---
## PHASE 3 : DÉVELOPPEMENT ET VALIDATION (TDD)

Tu peux maintenant commencer à coder.

1.  **Crée ta branche de travail :**
    *   `git checkout -b feature/[ID_US]-description-courte`
2.  **Écris un test qui échoue :** Conformément à la philosophie TDD, commence par écrire un test (PHPUnit ou Dusk) qui valide un critère d'acceptation et qui échoue.
3.  **Écris le code :** Implémente la fonctionnalité pour faire passer le test.
4.  **Répète et Refactorise :** Continue ce cycle jusqu'à ce que tous les critères d'acceptation soient couverts et que tous les tests passent.
5.  **Valide l'ensemble :** Lance `cd pifpaf && php artisan test` et `cd pifpaf && php artisan dusk` pour t'assurer de n'avoir introduit aucune régression.

---
## PHASE 4 : CLÔTURE ET DOCUMENTATION

Ton travail n'est terminé que lorsque la documentation est à jour.

1.  **Mets à jour la To-Do List :** Coche la case de ta User Story dans `doc/todo.md`.
2.  **Fournis les Preuves Visuelles :** Si ta mission a un impact sur l'interface, prends des captures d'écran (mobile et desktop).
    *   Crée le dossier : `mkdir -p doc/screenshots/[ID_US]`
    *   Sauvegarde les images dedans.
3.  **Soumets ton travail :** Rédige un message de commit clair et soumets ta Pull Request. Ton rapport doit inclure ton plan d'action final (de la phase 2) et les liens vers les captures d'écran.
