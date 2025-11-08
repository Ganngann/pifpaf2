@props(['completedSales'])

<div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <h3 class="text-2xl font-bold mb-6 text-center sm:text-left">Mes 5 dernières ventes terminées</h3>
        <div class="space-y-4">
            @foreach ($completedSales as $item)
                @php
                    $completedTransaction = $item->offers->where('status', 'paid')->first()->transaction;
                    $buyer = $completedTransaction->offer->user;
                @endphp
                <div class="p-4 border rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <a href="{{ route('items.show', $item) }}" class="flex items-center">
                         @if ($item->primaryImage && $item->primaryImage->path)
                            <img src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}" class="w-16 h-16 object-cover rounded mr-4">
                        @else
                            <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded mr-4">
                                <span class="text-gray-500 text-xs text-center">Aucune image</span>
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold">{{ $item->title }}</p>
                            <p class="text-sm text-gray-600">Acheteur : {{ $buyer->name }}</p>
                        </div>
                    </a>
                    <div class="mt-4 sm:mt-0">
                        @if ($completedTransaction->label_url)
                            <a href="{{ $completedTransaction->label_url }}" target="_blank" class="bg-gray-500 text-white font-bold py-2 px-4 rounded hover:bg-gray-700">Voir l'étiquette</a>
                        @else
                            <form action="{{ route('transactions.ship', $completedTransaction) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                                    Créer l'envoi
                                </button>
                            </form>
                        @endif
                        @if (!$completedTransaction->reviews()->where('reviewer_id', Auth::id())->exists())
                            <x-review-modal :transaction="$completedTransaction" :recipientName="$buyer->name" />
                        @else
                            <p class="text-sm text-gray-500">Avis déjà laissé.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 text-right">
            <a href="{{ route('transactions.sales') }}" class="text-blue-600 hover:underline">Voir toutes mes ventes</a>
        </div>
    </div>
</div>
