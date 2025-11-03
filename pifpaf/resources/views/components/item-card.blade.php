@props(['item'])

<div class="group relative rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out aspect-square">

    <!-- Image -->
    @if($item->primaryImage)
        <img src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}" class="absolute inset-0 w-full h-full object-cover">
    @else
        <img src="{{ asset('images/placeholder.jpg') }}" alt="Image placeholder" class="absolute inset-0 w-full h-full object-cover">
    @endif

    <!-- Overlay with text -->
    <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent text-white">
        <h3 class="font-bold text-lg truncate" title="{{ $item->title }}">{{ $item->title }}</h3>
        <div class="flex justify-between items-center mt-1">
            <p class="text-xl font-semibold">{{ number_format($item->price, 2, ',', ' ') }} €</p>
            <div class="text-sm">
                @if ($item->pickup_available && $item->pickupAddress)
                    <div class="flex items-center">
                        <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="truncate">{{ $item->pickupAddress->city }}</span>
                    </div>
                @elseif ($item->delivery_available)
                    <div class="flex items-center">
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

    <!-- Clickable link overlay -->
    <a href="{{ route('items.show', $item) }}" class="absolute inset-0 z-10">
        <span class="sr-only">Voir l'article {{ $item->title }}</span>
    </a>
</div>
