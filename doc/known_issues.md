# Problèmes Connus

Ce document liste les tests qui échouent ou sont désactivés dans la suite de tests de l'application.

## Suite de Tests Frontend (Laravel Dusk)

### Tests en échec

- **Suite:** `Tests\Browser\PaymentTest`
- **Test:** `a user can pay without wallet`
- **Erreur:** `NoSuchElementException`
- **Description:** Le test échoue car il ne trouve pas un élément attendu sur la page lors de la simulation d'un paiement par carte de crédit (sans utiliser le portefeuille).
- **Trace de l'erreur:**
  ```
   FAILED  Tests\Browser\PaymentTest > a user can pa…  NoSuchElementException
  no such element: Unable to locate element: {"method":"tag name","selector":"html"}
  (Session info: chrome=142.0.7444.59)

  at vendor/php-webdriver/webdriver/lib/Exception/WebDriverException.php:120
    116▕                     throw new NoSuchAlertException($message, $results);
    117▕                 case 'no such cookie':
    118▕                     throw new NoSuchCookieException($message, $results);
    119▕                 case 'no such element':
  ➜ 120▕                     throw new NoSuchElementException($message, $results);
    121▕                 case 'no such frame':
    122▕                     throw new NoSuchFrameException($message, $results);
    123▕                 case 'no such window':
    124▕                     throw new NoSuchWindowException($message, $results);

       [2m+5 vendor frames  [22m
  6   tests/Browser/PaymentTest.php:102
  ```

### Tests Désactivés (Skipped)

Les tests suivants sont intentionnellement désactivés (`skipped`) car ils concernent des fonctionnalités liées à l'IA qui sont en cours de développement ou de refactoring.

- `Tests\Browser\AiItemCreationDuskTest`
- `Tests\Browser\MultiObjectAiItemCreationTest`
- `Tests\Browser\ValidateAiSuggestionsTest`
