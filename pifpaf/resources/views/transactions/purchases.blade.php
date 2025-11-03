<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Achats') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @forelse ($purchases as $transaction)
                        <a href="{{ route('items.show', $transaction->offer->item) }}" class="flex items-center mb-4 border-b pb-4">
                            @if ($transaction->offer->item->primaryImage && $transaction->offer->item->primaryImage->path)
                                <img src="{{ asset('storage/' . $transaction->offer->item->primaryImage->path) }}" alt="{{ $transaction->offer->item->title }}" class="w-16 h-16 object-cover rounded">
                            @else
                                <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded">
                                    <span class="text-gray-500 text-xs text-center">Aucune image</span>
                                </div>
                            @endif
                            <div class="ml-4">
                                <div class="font-bold">{{ $transaction->offer->item->title }}</div>
                                <div>Vendu par : <a href="{{ route('profile.show', $transaction->offer->item->user) }}" class="text-blue-500 hover:underline">{{ $transaction->offer->item->user->name }}</a></div>
                                <div>{{ $transaction->amount }} €</div>
                                <div>{{ $transaction->created_at->format('d/m/Y') }}</div>
                                <div>Statut : {{ $transaction->status }}</div>
                            </div>
                        </a>
                    @empty
                        <p>Vous n'avez effectué aucun achat pour le moment.</p>
                    @endforelse

                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
