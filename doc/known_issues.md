# Problèmes Connus et Tests Défaillants

Ce document centralise les problèmes connus, les tests défaillants (failures) et les tests intentionnellement ignorés (skipped) dans la suite de tests du projet Pifpaf.

L'objectif est de fournir une vue d'ensemble claire de l'état de la base de code pour éviter de perdre du temps sur des problèmes déjà identifiés et pour prioriser les corrections.

---

## Tests PHPUnit (Back-End)

*Dernière exécution : 2025-11-07*

### Tests Ignorés (Skipped)

*Aucun test ignoré lors de la dernière exécution.*

### Tests Défaillants (Failures)

*Aucun test défaillant lors de la dernière exécution.*

---

## Tests Dusk (Front-End)

*Dernière exécution : 2025-11-07*

### Tests Ignorés (Skipped)

- **Test:** `Tests\Browser\AiItemCreationDuskTest`
  - **Raison :** "Skipping AI test due to external dependency"
  - **Fichier :** `pifpaf/tests/Browser/AiItemCreationDuskTest.php`

- **Test:** `Tests\Browser\BuyerConfirmsReceptionTest`
  - **Raison :** "Les tests de paiement sont désactivés car ils dépendent de services externes."
  - **Fichier :** `pifpaf/tests/Browser/BuyerConfirmsReceptionTest.php`

- **Test:** `Tests\Browser\MultiObjectAiItemCreationTest`
  - **Raison :** "Skipping this test as it is flaky and depends on AI service."
  - **Fichier :** `pifpaf/tests/Browser/MultiObjectAiItemCreationTest.php`

- **Test:** `Tests\Browser\PaymentFlowTest`
  - **Raison :** "Les tests de paiement sont désactivés pour éviter les appels API réels."
  - **Fichier :** `pifpaf/tests/Browser/PaymentFlowTest.php`

- **Test:** `Tests\Browser\PaymentTest` (3 tests)
  - **Raison :** "Les tests de paiement sont désactivés pour éviter les appels API réels."
  - **Fichier :** `pifpaf/tests/Browser/PaymentTest.php`

- **Test:** `Tests\Browser\ValidateAiSuggestionsTest`
  - **Raison :** "Skipping AI validation test."
  - **Fichier :** `pifpaf/tests/Browser/ValidateAiSuggestionsTest.php`

### Tests Défaillants (Failures)

*Aucun test défaillant lors de la dernière exécution.*
