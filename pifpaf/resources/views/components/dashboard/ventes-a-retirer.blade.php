@props(['soldItemsForPickup'])

<div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <h3 class="text-2xl font-bold mb-6 text-center sm:text-left">Ventes à retirer</h3>
        <div class="space-y-4">
            @foreach ($soldItemsForPickup as $item)
                @php
                    $paidOffer = $item->offers->firstWhere('status', 'paid');
                    $transaction = $paidOffer->transaction;
                    $buyer = $paidOffer->user;
                @endphp
                <div class="p-4 border rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <div class="flex items-center">
                        @if ($item->primary_image && $item->primary_image->path)
                            <img src="{{ asset('storage/' . $item->primary_image->path) }}" alt="{{ $item->title }}" class="w-16 h-16 object-cover rounded mr-4">
                        @else
                            <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded mr-4">
                                <span class="text-gray-500 text-xs text-center">Aucune image</span>
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold">{{ $item->title }}</p>
                            <p class="text-sm text-gray-600">Acheteur : {{ $buyer->name }}</p>
                            <p class="text-sm">
                                Code de retrait :
                                <span class="font-bold text-lg text-red-600 tracking-widest">{{ $transaction->pickup_code }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <form action="{{ route('transactions.confirm-pickup', $transaction) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                                Confirmer le retrait
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
