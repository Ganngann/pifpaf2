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
                        <div class="flex items-center mb-4">
                            <img src="{{ $transaction->offer->item->primaryImage ? Storage::url($transaction->offer->item->primaryImage->image_path) : asset('images/placeholder.jpg') }}" alt="{{ $transaction->offer->item->title }}" class="w-16 h-16 object-cover rounded">
                            <div class="ml-4">
                                <div class="font-bold">{{ $transaction->offer->item->title }}</div>
                                <div>{{ $transaction->amount }} €</div>
                                <div>{{ $transaction->created_at->format('d/m/Y') }}</div>
                                <div>Statut : {{ $transaction->status }}</div>
                            </div>
                        </div>
                    @empty
                        <p>Vous n'avez effectué aucun achat pour le moment.</p>
                    @endforelse

                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
