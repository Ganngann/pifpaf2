<x-mail::message>
# Virement effectué

Bonjour {{ $withdrawalRequest->user->name }},

Votre virement d'un montant de **{{ number_format($withdrawalRequest->amount, 2, ',', ' ') }} €** a été traité et est en route vers votre compte bancaire (IBAN : ...{{ substr($withdrawalRequest->bankAccount->iban, -4) }}).

Les fonds devraient apparaître sur votre compte d'ici 1 à 3 jours ouvrés, selon votre banque.

<x-mail::button :url="route('wallet.show')">
Voir l'historique de mes virements
</x-mail::button>

Merci d'utiliser {{ config('app.name') }} !<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
