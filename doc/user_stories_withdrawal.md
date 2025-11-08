# User Stories - Gestion des Virements

## Épopée : Retrait des fonds du portefeuille

Cette épopée couvre la capacité d'un utilisateur (vendeur) à retirer les fonds de son portefeuille vers son compte bancaire personnel.

---

### US-W1 : Enregistrement des informations bancaires

**En tant que** vendeur,
**Je veux** pouvoir enregistrer et gérer mes informations de paiement (coordonnées bancaires) de manière sécurisée,
**Afin de** pouvoir recevoir les fonds de mon portefeuille.

**Critères d'acceptation :**
- Un formulaire dédié permet de saisir les informations bancaires (IBAN, etc.).
- Les informations sont stockées de manière sécurisée (probablement via un service tiers comme Stripe Connect pour éviter de stocker des données sensibles).
- Je peux modifier ou supprimer mes informations bancaires.
- Le système valide le format des informations saisies.

---

### US-W2 : Demande de virement

**En tant que** vendeur,
**Je veux** pouvoir initier une demande de virement depuis la page de mon portefeuille,
**Afin de** transférer tout ou partie de mon solde disponible vers mon compte bancaire enregistré.

**Critères d'acceptation :**
- Sur la page de mon portefeuille, un bouton "Effectuer un virement" est visible.
- Un formulaire me permet de spécifier le montant que je souhaite retirer.
- Le montant demandé ne peut pas dépasser mon solde disponible.
- Une fois la demande soumise, un enregistrement de "demande de virement" est créé avec le statut "En attente".
- Le montant demandé est "gelé" dans mon portefeuille : il n'est plus disponible pour d'autres transactions, mais n'est pas encore débité.
- Je reçois une confirmation que ma demande a bien été prise en compte.

---

### US-W3 : Suivi du statut d'une demande de virement

**En tant que** vendeur,
**Je veux** voir l'historique de mes demandes de virement et leur statut (ex: En attente, En cours, Complété, Échoué),
**Afin de** suivre l'avancement de mes transferts de fonds.

**Critères d'acceptation :**
- Une section dans mon portefeuille liste toutes mes demandes de virement passées et présentes.
- Pour chaque demande, je peux voir le montant, la date et le statut actuel.
- Si une demande échoue, je peux voir la raison de l'échec (si disponible).

---

### US-W4 : Gestion et validation des demandes de virement (Admin)

**En tant qu'** administrateur,
**Je veux** accéder à un tableau de bord listant toutes les demandes de virement en attente,
**Afin de** pouvoir les valider ou les rejeter.

**Critères d'acceptation :**
- Une interface d'administration affiche la liste des demandes de virement avec le statut "En attente".
- Je peux consulter les détails de chaque demande (utilisateur, montant, date).
- Je dispose de boutons pour "Approuver" ou "Rejeter" une demande.
- Si je rejette une demande, je peux fournir une raison. Les fonds gelés sont alors restitués au portefeuille de l'utilisateur.
- Si j'approuve une demande, le statut passe à "En cours de traitement" et le processus de virement est initié.

---

### US-W5 : Traitement automatisé du virement

**En tant que** système,
**Je veux** interagir avec le service de paiement (Stripe) pour exécuter un virement approuvé,
**Afin de** transférer les fonds vers le compte bancaire de l'utilisateur.

**Critères d'acceptation :**
- Lorsqu'une demande est approuvée, un appel API est fait au service de paiement pour initier le transfert.
- Le système écoute les retours (webhooks) du service de paiement pour connaître le statut du transfert.
- Si le transfert réussit, le statut de la demande passe à "Complété", et le montant est définitivement débité du portefeuille de l'utilisateur.
- Si le transfert échoue, le statut de la demande passe à "Échoué", la raison est enregistrée, et les fonds sont restitués au portefeuille de l'utilisateur.

---

### US-W6 : Notifications par email

**En tant que** vendeur,
**Je veux** recevoir des notifications par email concernant mes demandes de virement,
**Afin d'**être informé des étapes importantes.

**Critères d'acceptation :**
- Je reçois un email de confirmation lorsque je soumets une demande.
- Je reçois un email lorsque ma demande est approuvée et en cours de traitement.
- Je reçois un email pour m'informer du succès ou de l'échec du virement.
