@props(['transaction', 'viewpoint' => 'buyer'])

@php
    $statusText = '';
    $statusClass = 'text-gray-600';

    switch ($transaction->status) {
        case \App\Enums\TransactionStatus::PAYMENT_RECEIVED:
            if ($transaction->offer->delivery_method === 'pickup') {
                $statusText = 'Paiement reçu - En attente de retrait';
                $statusClass = 'text-yellow-600';
            } else {
                $statusText = 'Paiement reçu - En attente d\'expédition';
                $statusClass = 'text-yellow-600';
            }
            break;
        case \App\Enums\TransactionStatus::PICKUP_COMPLETED:
             $statusText = 'Retrait effectué';
             $statusClass = 'text-green-600';
             break;
        case \App\Enums\TransactionStatus::COMPLETED:
            $statusText = 'Transaction terminée';
            $statusClass = 'text-green-600';
            break;
        case \App\Enums\TransactionStatus::DISPUTED:
            $statusText = 'Litige en cours';
            $statusClass = 'text-red-600';
            break;
        default:
            if ($transaction->offer->status === 'accepted') {
                $statusText = 'Offre acceptée - En attente de paiement';
                $statusClass = 'text-orange-500';
            } else {
                 $statusText = 'En attente';
            }
    }
@endphp

<p class="text-sm mt-1">
    Statut : <span class="font-semibold {{ $statusClass }}">{{ $statusText }}</span>
</p>
