@props(['item'])

<div class="group relative rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out">
    <a href="{{ route('items.show', $item) }}" class="absolute inset-0 z-10">
        <span class="sr-only">Voir l'article {{ $item->title }}</span>
    </a>

    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden">
        <x-ui.item-thumbnail :item="$item" class="w-full h-full object-cover" />
    </div>

    <div class="p-4 bg-white">
        <h3 class="font-bold text-lg truncate text-gray-800" title="{{ $item->title }}">
            <a href="{{ route('items.show', $item) }}" class="hover:underline">
                {{ $item->title }}
            </a>
        </h3>

        <div class="mt-2">
             <x-ui.user-profile-link :user="$item->user" />
        </div>

        <div class="flex justify-between items-center mt-3">
            <p class="text-xl font-semibold text-gray-900">{{ number_format($item->price, 2, ',', ' ') }} €</p>

            <div class="text-sm text-gray-600">
                @if ($item->pickup_available && $item->pickupAddress)
                    <div class="flex items-center" title="Remise en main propre à {{ $item->pickupAddress->city }}">
                        <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="truncate">{{ $item->pickupAddress->city }}</span>
                    </div>
                @elseif ($item->delivery_available)
                    <div class="flex items-center" title="Livraison disponible">
                         <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h8a1 1 0 001-1zM21 11V5a2 2 0 00-2-2H9.5a2 2 0 00-2 2v2" />
                        </svg>
                        <span>Livraison</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
