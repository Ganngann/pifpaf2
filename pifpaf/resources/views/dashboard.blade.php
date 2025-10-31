<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($items->isEmpty())
                        <div class="text-center text-gray-500">
                            <p>Vous n'avez pas encore d'annonce.</p>
                            <a href="{{ route('items.create') }}" class="mt-2 inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                                Créer ma première annonce
                            </a>
                        </div>
                    @else
                        <h3 class="text-2xl font-bold mb-6 text-center sm:text-left">Mes annonces</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($items as $item)
                                <div class="border rounded-lg shadow-lg overflow-hidden flex flex-col">

                                    <div class="relative">
                                    @if ($item->images->isNotEmpty())
                                        <img src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500">Aucune image</span>
                                        </div>
                                    @endif
                                    <span @class([
                                            'absolute top-2 left-2 text-xs font-bold px-2 py-1 rounded text-white',
                                            'bg-green-500' => $item->status === \App\Enums\ItemStatus::AVAILABLE,
                                            'bg-gray-500' => $item->status === \App\Enums\ItemStatus::UNPUBLISHED,
                                            'bg-blue-500' => $item->status === \App\Enums\ItemStatus::SOLD,
                                        ])>
                                            @if($item->status === \App\Enums\ItemStatus::AVAILABLE)
                                                En ligne
                                            @elseif($item->status === \App\Enums\ItemStatus::UNPUBLISHED)
                                                Hors ligne
                                            @else
                                                Vendu
                                            @endif
                                        </span>
                                    </div>
                                    <div class="p-4 flex flex-col flex-grow">
                                        <h4 class="text-xl font-semibold">{{ $item->title }}</h4>
                                        <p class="text-gray-700 mt-2 flex-grow">{{ Str::limit($item->description, 100) }}</p>
                                        <p class="text-lg font-bold text-gray-900 mt-4">{{ number_format($item->price, 2, ',', ' ') }} €</p>
                                        <div class="mt-4 flex flex-wrap justify-end gap-2">
                                            @if ($item->status === \App\Enums\ItemStatus::AVAILABLE)
                                                <form action="{{ route('items.unpublish', $item) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment dépublier cette annonce ?');">
                                                    @csrf
                                                    <button type="submit" class="text-sm bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Dépublier</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('items.edit', $item) }}" class="text-sm bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Modifier</a>
                                            <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Supprimer</button>
                                            </form>
                                        </div>
                                    </div>
                                    {{-- Section des offres reçues --}}
                                    @if($item->status === \App\Enums\ItemStatus::AVAILABLE && $item->offers->where('status', 'pending')->isNotEmpty())
                                        <div class="p-4 border-t bg-gray-50">
                                            <h5 class="font-semibold text-gray-700">Offres reçues :</h5>
                                            <ul class="mt-2 space-y-2">
                                                @foreach ($item->offers->where('status', 'pending') as $offer)
                                                    <li class="flex items-center justify-between text-sm">
                                                        <span>{{ $offer->user->name }} - <span class="font-bold">{{ number_format($offer->amount, 2, ',', ' ') }} €</span></span>
                                                        <div class="flex space-x-1">
                                                            <form action="{{ route('offers.accept', $offer) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="bg-green-500 text-white px-2 py-1 text-xs rounded hover:bg-green-600">Accepter</button>
                                                            </form>
                                                            <form action="{{ route('offers.reject', $offer) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="bg-red-500 text-white px-2 py-1 text-xs rounded hover:bg-red-600">Refuser</button>
                                                            </form>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Section Ventes à retirer --}}
            @php
                $soldItemsForPickup = $items->filter(function ($item) {
                    if ($item->status !== \App\Enums\ItemStatus::SOLD || !$item->pickup_available) {
                        return false;
                    }
                    $paidOffer = $item->offers->firstWhere('status', 'paid');
                    return $paidOffer && $paidOffer->transaction && $paidOffer->transaction->status !== 'pickup_completed';
                });
            @endphp
            @if ($soldItemsForPickup->isNotEmpty())
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
                                        @if ($item->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->title }}" class="w-16 h-16 object-cover rounded mr-4">
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
            @endif

            {{-- Section Mes Offres --}}
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-2xl font-bold mb-6 text-center sm:text-left">Mes offres</h3>
                    @if ($offers->isEmpty())
                        <div class="text-center text-gray-500">
                            <p>Vous n'avez fait aucune offre pour le moment.</p>
                            <a href="{{ route('welcome') }}" class="mt-2 inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                                Voir les articles
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($offers as $offer)
                                <div class="p-4 border rounded-lg flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold">
                                            Article : <a href="{{ route('items.show', $offer->item) }}" class="text-blue-600 hover:underline">{{ $offer->item->title }}</a>
                                        </p>
                                        <p>Votre offre : <span class="font-bold">{{ number_format($offer->amount, 2, ',', ' ') }} €</span></p>
                                        <p>Statut :
                                            <span @class([
                                                'font-semibold',
                                                'text-yellow-600' => $offer->status === 'pending',
                                                'text-green-600' => $offer->status === 'accepted' || $offer->status === 'paid',
                                                'text-red-600' => $offer->status === 'rejected',
                                            ])>
                                                @if($offer->status === 'paid')
                                                    Payée
                                                @else
                                                    {{ ucfirst($offer->status) }}
                                                @endif
                                            </span>
                                        </p>
                                        @if($offer->status === 'paid' && $offer->item->pickup_available && $offer->transaction)
                                            <p class="mt-2 text-sm">
                                                Code de retrait : <span class="font-bold text-lg text-green-600 tracking-widest">{{ $offer->transaction->pickup_code }}</span>
                                            </p>
                                        @endif
                                    </div>
                                    @if ($offer->status === 'accepted')
                                        <a href="{{ route('payment.create', $offer) }}" dusk="pay-offer-{{ $offer->id }}" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-700">
                                            Payer
                                        </a>
                                    @elseif ($offer->status === 'paid' && $offer->transaction && $offer->transaction->status === 'payment_received')
                                        <form action="{{ route('transactions.confirm-reception', $offer->transaction) }}" method="POST" onsubmit="return confirm('Veuillez confirmer que vous avez bien reçu l\'article. Cette action est irréversible et transférera les fonds au vendeur.');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-700">
                                                Confirmer la réception
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
