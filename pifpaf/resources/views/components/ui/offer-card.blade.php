@props(['offer', 'viewpoint' => 'buyer'])

@php
    $isSellerView = $viewpoint === 'seller';
    $item = $offer->item;
    $userToShow = $isSellerView ? $offer->user : $item->user;
@endphp

<div class="p-4 border rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div class="flex items-center w-full sm:w-auto">
        <a href="{{ route('items.show', $item) }}">
             <x-ui.item-thumbnail :item="$item" class="w-16 h-16 object-cover rounded" />
        </a>
        <div class="ml-4 flex-grow">
            <p class="font-semibold">
                <a href="{{ route('items.show', $item) }}" class="text-blue-600 hover:underline">{{ $item->title }}</a>
            </p>
            <p class="text-sm text-gray-600">
                {{ $isSellerView ? 'Offre de' : 'Vendu par' }}: <x-ui.user-profile-link :user="$userToShow" />
            </p>
             <p class="text-sm mt-1">
                {{ $isSellerView ? 'Montant de l\'offre' : 'Votre offre' }}:
                <span class="font-bold">{{ number_format($offer->amount, 2, ',', ' ') }} €</span>
            </p>
        </div>
    </div>

    <div class="flex flex-col items-start sm:items-end w-full sm:w-auto mt-2 sm:mt-0">
         <div class="text-sm">
            Statut :
            <span @class([
                'font-semibold',
                'text-yellow-600' => $offer->status === 'pending',
                'text-green-600' => in_array($offer->status, ['accepted', 'paid']),
                'text-red-600' => $offer->status === 'rejected',
            ])>
                @if($offer->status === 'paid')
                    Payée
                @elseif($offer->status === 'pending')
                    En attente
                @elseif($offer->status === 'accepted')
                    Acceptée
                @elseif($offer->status === 'rejected')
                    Refusée
                @else
                    {{ ucfirst($offer->status) }}
                @endif
            </span>
        </div>

        @if(isset($actions))
            <div class="mt-2 flex items-center space-x-2">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
