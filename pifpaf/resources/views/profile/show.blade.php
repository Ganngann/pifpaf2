<x-main-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden p-8">
            <h1 class="text-4xl font-bold mb-2">{{ $user->name }}</h1>

            {{-- Ratings and reviews --}}
            <div class="mb-6">
                @if ($reviewCount > 0)
                    <div class="flex items-center">
                        <span class="text-gray-500 mr-2">Note moyenne :</span>
                        <span class="font-semibold text-yellow-500">{{ number_format($averageRating, 1) }} / 5</span>
                        <span class="text-gray-400 ml-2">({{ $reviewCount }} avis)</span>
                    </div>
                @else
                    <p class="text-gray-500">Aucun avis pour le moment.</p>
                @endif
            </div>

            <div class="mt-8 pt-8 border-t">
                <h2 class="text-2xl font-bold mb-4">Avis reçus</h2>
                @forelse($user->reviewsReceived as $review)
                    <div class="border-b py-4">
                        <div class="flex items-center mb-2">
                            <span class="font-semibold">{{ $review->reviewer->name }}</span>
                            <span class="text-gray-400 mx-2">-</span>
                            <span class="text-yellow-500 font-bold">{{ $review->rating }}/5</span>
                        </div>
                        <p class="text-gray-700">{{ $review->comment }}</p>
                    </div>
                @empty
                    <p>Cet utilisateur n'a pas encore reçu d'avis.</p>
                @endforelse
            </div>

            <div class="mt-8 pt-8 border-t">
                <h2 class="text-2xl font-bold mb-4">Articles en vente</h2>
                @if($user->items->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                        @foreach($user->items as $item)
                            <x-item-card :item="$item" />
                        @endforeach
                    </div>
                @else
                    <p class="mt-4">Cet utilisateur n'a aucun article en vente pour le moment.</p>
                @endif
            </div>
        </div>
    </div>
</x-main-layout>
