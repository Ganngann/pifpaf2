@props(['transaction'])

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
        <div>
            <span class="text-sm text-gray-600">Vente du {{ $transaction->created_at->format('d/m/Y') }}</span>
        </div>
        <span class="px-2 py-1 text-xs font-semibold rounded-full
            @switch($transaction->status)
                @case('payment_received') bg-blue-100 text-blue-800 @break
                @case('shipping_initiated') bg-yellow-100 text-yellow-800 @break
                @case('completed') bg-green-100 text-green-800 @break
                @default bg-gray-100 text-gray-800
            @endswitch
        ">
            {{ Str::ucfirst(str_replace('_', ' ', $transaction->status)) }}
        </span>
    </div>

    <div class="p-4 flex">
        <div class="flex-shrink-0 mr-4">
            <a href="{{ route('items.show', $transaction->offer->item) }}">
                <x-ui.item-thumbnail :item="$transaction->offer->item" class="w-24 h-24 rounded-md" />
            </a>
        </div>
        <div class="flex-grow">
            <a href="{{ route('items.show', $transaction->offer->item) }}" class="text-lg font-bold hover:underline">{{ $transaction->offer->item->title }}</a>
            <p class="text-xl font-light text-gray-800">{{ number_format($transaction->amount, 2, ',', ' ') }} €</p>
            <div class="mt-2 text-sm text-gray-600">
                <span>Acheté par :</span>
                <x-ui.user-profile-link :user="$transaction->offer->user" />
            </div>
        </div>
    </div>

    <div class="p-4 bg-gray-50 border-t">
        <div class="flex items-center justify-end space-x-2">
            @if ($transaction->status === 'payment_received' && !$transaction->label_url && $transaction->offer->item->delivery_available)
                <form action="{{ route('transactions.ship', $transaction) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Générer l'étiquette d'envoi
                    </button>
                </form>
            @endif

            @if ($transaction->label_url)
                <a href="{{ $transaction->label_url }}" target="_blank" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Télécharger l'étiquette
                </a>
            @endif

            {{-- Placeholder for future actions --}}
             <a href="#" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Contacter l'acheteur
            </a>
        </div>
    </div>
</div>
