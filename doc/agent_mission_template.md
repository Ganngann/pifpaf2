# Modèle de Mission pour Agent de Développement Jules

**OBJECTIF DE LA TÂCHE :** `[Description claire et concise de la mission]`

---
## PHASE 0 : PRÉPARATION SYSTÈME

Ta première action est de garantir un environnement de travail sain et à jour.

1.  **Exécute le script de configuration :**
    ```bash
    bash setup.sh
    ```
2.  **Vérifie la sortie :** Assure-toi que le script se termine sans erreur fatale.

---
## PHASE 1 : ASSIMILATION DU CONTEXTE GLOBAL (ACTION LA PLUS IMPORTANTE)

Ton but est de comprendre parfaitement ta mission et son écosystème. Une analyse précipitée conduit à un travail de mauvaise qualité.

1.  **Lis la Documentation Stratégique :**
    *   `README.md` : Pour la vision générale et les standards.
    *   `doc/feuille_de_route.md` : Pour comprendre où se situe ta tâche dans la stratégie globale.
    *   `doc/todo.md` : Pour connaître les priorités actuelles.
    *   `doc/known_issues.md` : Pour être conscient des problèmes et des tests défaillants existants. Ne perds pas de temps sur un problème déjà identifié.
    *   Tout autre doccument qui pourait etre utile dans le cadre de la tache.

2.  **Analyse les Spécifications de la Tâche (si applicable) :**
    *   Si ta mission est liée à une **User Story**, trouve-la dans `doc/user_stories.md` et étudie ses critères d'acceptation.
    *   Si une **Fiche Technique** existe dans `doc/technical_sheets/`, lis-la attentivement. **Considère-la comme une suggestion, pas comme une vérité absolue.**

---
## PHASE 2 : ANALYSE STRATÉGIQUE DU CODE EXISTANT

Ta mission n'existe pas dans le vide. Ton code doit s'intégrer parfaitement à l'existant.

1.  **Cartographie du Terrain (en suivant le flux Laravel) :**
    *   **Routes :** Analyse `pifpaf/routes/web.php` et `pifpaf/routes/api.php` pour identifier les points d'entrée liés à ta mission.
    *   **Controllers :** Étudie les contrôleurs concernés pour comprendre la logique applicative.
    *   **Models :** Analyse les modèles Eloquent, leurs propriétés (`$fillable`, `$casts`) et surtout leurs **relations**.
    *   **Views & Components :** Si la mission a un impact sur l'UI, identifie les vues Blade et les **composants** (`pifpaf/resources/views/components/`) qu'elles utilisent.
    *   **Migrations :** Consulte les migrations (`pifpaf/database/migrations/`) pour comprendre la structure exacte de la base de données.

2.  **Analyse d'Impact :**
    *   Confronte la solution que tu envisages avec ce que tu viens d'apprendre.
    *   Pose-toi les questions suivantes :
        *   *Puis-je réutiliser une relation, un scope, un service ou un composant existant ?* **NE RÉINVENTE PAS LA ROUE.**
        *   *La solution envisagée est-elle la plus simple et la plus maintenable ?*
        *   *Mon changement risque-t-il d'impacter une autre partie de l'application ? (Ex: casser le style, modifier le comportement d'un composant AlpineJS)*

---
## PHASE 3 : DIALOGUE ET PLANIFICATION FINALE

Avant d'écrire la moindre ligne de code, valide ta compréhension.

1.  **Pose des Questions :**
    *   S'il y a la moindre ambiguïté, la moindre incertitude, **demande des clarifications**. Mieux vaut une question maintenant qu'une réécriture complète plus tard.

2.  **Formalise ton Plan d'Action Final :**
    *   Sur la base de ton analyse approfondie, rédige un plan d'action clair (autant points de points que nécéssaire).
    *   Ce plan doit mentionner les fichiers que tu comptes créer ou modifier. Si tu dévies d'une fiche technique, justifie-le.

---
## PHASE 4 : DÉVELOPPEMENT GUIDÉ PAR LES COMPOSANTS (TDD)

Tu peux maintenant commencer à coder.

**Principe de Conception : L'Architecture par Composants**
*   **Fractionne le code :** Vise toujours à créer les composants les plus petits et les plus réutilisables possible. Chaque élément (Classe PHP, Méthode, Composant Blade, Store AlpineJS) doit avoir **une seule et unique responsabilité.**
*   **Pense "Styleguide" :** Si tu crées un composant d'interface, il doit être suffisamment générique pour pouvoir être testé et affiché dans le `styleguide` de l'application.

1.  **Crée ta branche de travail :**
    *   `git checkout -b feature/nom-de-la-tache`
2.  **Écris un test qui échoue :** Conformément à la philosophie TDD, commence par écrire un test (PHPUnit ou Dusk) qui valide un aspect de ta tâche et qui échoue.
3.  **Écris le code :** Implémente la fonctionnalité pour faire passer le test.
4.  **Répète et Refactorise :** Continue ce cycle jusqu'à ce que la fonctionnalité soit complète, et que le code soit propre et bien structuré.
5.  **Valide l'ensemble :** Lance `cd pifpaf && php artisan test` et `cd pifpaf && php artisan dusk` pour t'assurer de n'avoir introduit aucune régression.

---
## PHASE 5 : CLÔTURE ET DOCUMENTATION

Ton travail n'est terminé que lorsque la documentation est à jour.

1.  **Mets à jour la Documentation :** Si ta tâche était liée à une User Story, coche la case correspondante dans `doc/todo.md`.
2.  **Soumets ton travail :** Rédige un message de commit clair et soumets ta Pull Request. Ton rapport doit inclure ton plan d'action final (de la phase 3). Si ta tache concerne un issue, mentionne le de facon a ce que il se ferme automatiquement a la prochaine pr.
