@props(['item'])

<div class="mb-6">
    <h2 class="text-xl font-semibold mb-3">Modes de livraison</h2>
    <div class="space-y-4">
        @if ($item->pickup_available && $item->pickupAddress)
            <div class="p-4 bg-gray-50 rounded-lg border">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-800">Retrait sur place</p>
                        <p class="text-sm text-gray-500">À {{ $item->pickupAddress->city }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($item->delivery_available)
            <div class="p-4 bg-gray-50 rounded-lg border">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h8a1 1 0 001-1zM21 11V5a2 2 0 00-2-2H9.5a2 2 0 00-2 2v2" />
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-800">Livraison</p>
                        <p class="text-sm text-gray-500">Envoi par colis postal.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
