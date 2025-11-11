<x-mail::message>
# Demande de virement approuvée

Bonjour {{ $withdrawalRequest->user->name }},

Bonne nouvelle ! Votre demande de virement d'un montant de **{{ number_format($withdrawalRequest->amount, 2, ',', ' ') }} €** a été approuvée.

Le traitement du virement vers votre compte bancaire (IBAN : ...{{ substr($withdrawalRequest->bankAccount->iban, -4) }}) sera effectué sous peu.

Vous pouvez consulter le statut de vos demandes à tout moment depuis votre espace portefeuille.

<x-mail::button :url="route('wallet.show')">
Accéder à mon portefeuille
</x-mail::button>

Merci,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
