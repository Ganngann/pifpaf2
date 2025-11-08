@props(['transaction', 'viewpoint' => 'buyer'])

@php
    $item = $transaction->offer->item;
    $offer = $transaction->offer;
    $isBuyer = ($viewpoint === 'buyer');
    $isSeller = ($viewpoint === 'seller');
@endphp

<div class="p-4 border rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between">
    <div class="flex items-center mb-4 sm:mb-0">
        <a href="{{ route('items.show', $item) }}">
            @if ($item->primaryImage && $item->primaryImage->path)
                <img src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}" class="w-16 h-16 object-cover rounded mr-4">
            @else
                <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded mr-4">
                    <span class="text-gray-500 text-xs text-center">?</span>
                </div>
            @endif
        </a>
        <div>
            <p class="font-semibold text-lg">
                <a href="{{ route('items.show', $item) }}" class="hover:text-blue-600 transition-colors">{{ $item->title }}</a>
            </p>
            @if ($isBuyer)
                <p class="text-sm text-gray-600">Vendeur : <a href="{{ route('profile.show', $item->user) }}" class="text-blue-600 hover:underline">{{ $item->user->name }}</a></p>
            @else
                <p class="text-sm text-gray-600">Acheteur : <a href="{{ route('profile.show', $offer->user) }}" class="text-blue-600 hover:underline">{{ $offer->user->name }}</a></p>
            @endif
            <p class="text-sm">Votre offre : <span class="font-bold">{{ number_format($offer->amount, 2, ',', ' ') }} €</span></p>

            {{-- Statut de la transaction --}}
            <x-dashboard.transaction-status :transaction="$transaction" :viewpoint="$viewpoint" />

            @if ($isBuyer && $transaction->status === \App\Enums\TransactionStatus::PAYMENT_RECEIVED && $offer->delivery_method === 'pickup')
                <p class="mt-2 text-sm">
                    Code de retrait : <span class="font-bold text-lg text-green-600 tracking-widest">{{ $transaction->pickup_code }}</span>
                </p>
            @endif
        </div>
    </div>

    {{-- Actions possibles --}}
    <div class="flex flex-col items-stretch sm:items-end space-y-2">
        @if ($isBuyer)
            {{-- Actions pour l'acheteur --}}
            @if ($offer->status === 'accepted')
                <a href="{{ route('payment.create', $offer) }}" dusk="pay-offer-{{ $offer->id }}" class="btn-primary">
                    Payer
                </a>
            @elseif ($transaction->status === \App\Enums\TransactionStatus::PAYMENT_RECEIVED && $offer->delivery_method === 'pickup')
                <p class="text-sm text-gray-600">En attente du retrait par le vendeur.</p>
            @elseif ($transaction->status === \App\Enums\TransactionStatus::PAYMENT_RECEIVED && $offer->delivery_method === 'delivery')
                 <form action="{{ route('transactions.confirm-reception', $transaction) }}" method="POST" onsubmit="return confirm('Veuillez confirmer que vous avez bien reçu l\'article. Cette action est irréversible et transférera les fonds au vendeur.');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-primary-outline">
                        Confirmer la réception
                    </button>
                </form>
            @elseif ($transaction->status === \App\Enums\TransactionStatus::SHIPPING_INITIATED)
                 <a href="#" class="btn-secondary">Suivre le colis</a> {{-- Lien de suivi à implémenter --}}
            @endif

        @else
            {{-- Actions pour le vendeur --}}
            @if ($transaction->status === \App\Enums\TransactionStatus::PAYMENT_RECEIVED && $offer->delivery_method === 'pickup')
                 <form action="{{ route('transactions.confirm-pickup', $transaction) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-primary-outline">
                        Confirmer le retrait
                    </button>
                </form>
            @elseif ($transaction->status === \App\Enums\TransactionStatus::PAYMENT_RECEIVED && $offer->delivery_method === 'delivery')
                @if ($transaction->label_url)
                    <a href="{{ $transaction->label_url }}" target="_blank" class="btn-secondary">Voir l'étiquette</a>
                @else
                    <form action="{{ route('transactions.ship', $transaction) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary">
                            Créer l'envoi
                        </button>
                    </form>
                @endif
            @elseif ($transaction->status === \App\Enums\TransactionStatus::SHIPPING_INITIATED)
                 <a href="#" class="btn-secondary">Suivre le colis</a> {{-- Lien de suivi à implémenter --}}
            @endif
        @endif
    </div>
</div>
