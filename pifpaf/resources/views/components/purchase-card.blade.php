@props(['transaction'])

<div class="bg-white rounded-lg shadow-md overflow-hidden mb-4 flex flex-col sm:flex-row">
    <!-- Image -->
    <a href="{{ route('items.show', $transaction->offer->item) }}" class="sm:w-32 md:w-48 flex-shrink-0">
        @if ($transaction->offer->item->primaryImage)
            <img class="w-full h-32 sm:h-full object-cover" src="{{ asset('storage/' . $transaction->offer->item->primaryImage->path) }}" alt="{{ $transaction->offer->item->title }}">
        @else
            <div class="w-full h-32 sm:h-full bg-gray-200 flex items-center justify-center">
                <span class="text-gray-500 text-xs text-center">Aucune image</span>
            </div>
        @endif
    </a>

    <!-- Details -->
    <div class="p-4 flex flex-col flex-grow">
        <div>
            <h3 class="font-bold text-lg">
                <a href="{{ route('items.show', $transaction->offer->item) }}" class="hover:underline">{{ $transaction->offer->item->title }}</a>
            </h3>
            <p class="text-sm text-gray-600">
                Vendu par : <a href="{{ route('profile.show', $transaction->offer->item->user) }}" class="text-blue-500 hover:underline">{{ $transaction->offer->item->user->name }}</a>
            </p>
        </div>

        <div class="mt-2 flex-grow">
            <div class="flex items-center justify-between text-sm text-gray-800">
                <span>Prix payé</span>
                <span class="font-semibold">{{ number_format($transaction->amount, 2, ',', ' ') }} €</span>
            </div>
            <div class="flex items-center justify-between text-sm text-gray-800 mt-1">
                <span>Date de l'achat</span>
                <span>{{ $transaction->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="flex items-center justify-between text-sm text-gray-800 mt-1">
                <span>Statut</span>
                <span class="font-semibold px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-xs">{{ $transaction->status }}</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap items-center justify-end gap-2">
            <a href="{{ route('transactions.show', $transaction) }}" class="text-sm text-blue-500 hover:underline">Voir les détails</a>
            <a href="#" class="text-sm text-blue-500 hover:underline">Contacter le vendeur</a>

            @if ($transaction->label_url)
                <a href="{{ $transaction->label_url }}" target="_blank" class="px-3 py-1 bg-gray-200 text-gray-800 rounded-md text-sm hover:bg-gray-300">
                    Voir l'étiquette
                </a>
            @endif

            @if ($transaction->status === 'payment_received')
                <form action="{{ route('transactions.confirm-reception', $transaction) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded-md text-sm hover:bg-green-600">
                        Confirmer la réception
                    </button>
                </form>
            @endif

            @if ($transaction->status === 'completed' && !$transaction->reviews->contains('reviewer_id', Auth::id()))
                <x-review-modal :transaction="$transaction" :recipientName="$transaction->offer->item->user->name" />
            @endif
        </div>
    </div>
</div>
