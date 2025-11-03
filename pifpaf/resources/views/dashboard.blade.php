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

                        <!-- Vue Tableau pour Desktop -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Annonce
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Statut
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Prix
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($items as $item)
                                        <tr id="item-row-{{ $item->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if ($item->primaryImage && $item->primaryImage->path)
                                                            <img class="h-10 w-10 object-cover" src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}">
                                                        @else
                                                            <div class="h-10 w-10 bg-gray-200 flex items-center justify-center">
                                                                <span class="text-xs text-gray-500">?</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <a href="{{ route('items.edit', $item) }}" class="hover:text-blue-600 transition-colors">{{ $item->title }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                 <span id="status-badge-{{ $item->id }}" @class([
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    'bg-green-100 text-green-800' => $item->status === \App\Enums\ItemStatus::AVAILABLE,
                                                    'bg-gray-100 text-gray-800' => $item->status === \App\Enums\ItemStatus::UNPUBLISHED,
                                                    'bg-blue-100 text-blue-800' => $item->status === \App\Enums\ItemStatus::SOLD,
                                                ])>
                                                    @if($item->status === \App\Enums\ItemStatus::AVAILABLE)
                                                        En ligne
                                                    @elseif($item->status === \App\Enums\ItemStatus::UNPUBLISHED)
                                                        Hors ligne
                                                    @else
                                                        Vendu
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ number_format($item->price, 2, ',', ' ') }} €
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-2" id="actions-{{ $item->id }}">
                                                    @if ($item->status !== \App\Enums\ItemStatus::SOLD)
                                                        <button
                                                            data-item-id="{{ $item->id }}"
                                                            class="toggle-status-btn text-yellow-600 hover:text-yellow-900">
                                                            {{ $item->status === \App\Enums\ItemStatus::AVAILABLE ? 'Dépublier' : 'Publier' }}
                                                        </button>
                                                    @endif
                                                     <a href="{{ route('items.show', $item) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                                    <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                         {{-- Section des offres pour cet item --}}
                                        @if($item->status === \App\Enums\ItemStatus::AVAILABLE && $item->offers->where('status', 'pending')->isNotEmpty())
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 bg-gray-50">
                                                    <div class="pl-10">
                                                        <h5 class="text-sm font-semibold text-gray-700">Offres reçues :</h5>
                                                        <ul class="mt-2 space-y-2">
                                                            @foreach ($item->offers->where('status', 'pending') as $offer)
                                                                <li class="p-2 border-b last:border-b-0">
                                                                    <div class="flex items-center justify-between text-sm mb-1">
                                                                        <span>
                                                                            Acheteur : <a href="{{ route('profile.show', $offer->user) }}" class="text-blue-600 hover:underline">{{ $offer->user->name }}</a> - <span class="font-bold">{{ number_format($offer->amount, 2, ',', ' ') }} €</span>
                                                                        </span>
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
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Vue Cartes pour Mobile -->
                        <div class="sm:hidden space-y-4">
                            @foreach ($items as $item)
                                <div class="border rounded-lg shadow-lg overflow-hidden">
                                    <a href="{{ route('items.edit', $item) }}" class="block p-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-16 w-16">
                                                @if ($item->primaryImage && $item->primaryImage->path)
                                                    <img class="h-16 w-16 object-cover" src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}">
                                                @else
                                                     <div class="h-16 w-16 bg-gray-200 flex items-center justify-center">
                                                        <span class="text-xs text-gray-500">?</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4 flex-grow">
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $item->title }}</h4>
                                                <p class="text-sm font-bold text-gray-700 mt-1">{{ number_format($item->price, 2, ',', ' ') }} €</p>
                                                <span @class([
                                                    'mt-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    'bg-green-100 text-green-800' => $item->status === \App\Enums\ItemStatus::AVAILABLE,
                                                    'bg-gray-100 text-gray-800' => $item->status === \App\Enums\ItemStatus::UNPUBLISHED,
                                                    'bg-blue-100 text-blue-800' => $item->status === \App\Enums\ItemStatus::SOLD,
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
                                        </div>
                                    </a>
                                     <div class="p-4 border-t flex flex-wrap justify-end gap-2 bg-gray-50">
                                            @if ($item->status !== \App\Enums\ItemStatus::SOLD)
                                                <button
                                                    data-item-id="{{ $item->id }}"
                                                    class="toggle-status-btn text-sm bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                                    {{ $item->status === \App\Enums\ItemStatus::AVAILABLE ? 'Dépublier' : 'Publier' }}
                                                </button>
                                            @endif
                                            <a href="{{ route('items.show', $item) }}" class="text-sm bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Voir</a>
                                            <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Supprimer</button>
                                            </form>
                                        </div>
                                    {{-- Section des offres pour cet item --}}
                                    @if($item->status === \App\Enums\ItemStatus::AVAILABLE && $item->offers->where('status', 'pending')->isNotEmpty())
                                        <div class="p-4 border-t bg-gray-50">
                                            <h5 class="font-semibold text-gray-700 text-sm">Offres reçues :</h5>
                                            <ul class="mt-2 space-y-2">
                                                @foreach ($item->offers->where('status', 'pending') as $offer)
                                                    <li class="p-2 border-b last:border-b-0">
                                                        <div class="flex items-center justify-between text-sm mb-1">
                                                            <span>
                                                                <a href="{{ route('profile.show', $offer->user) }}" class="text-blue-600 hover:underline">{{ $offer->user->name }}</a> - <span class="font-bold">{{ number_format($offer->amount, 2, ',', ' ') }} €</span>
                                                            </span>
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
                                    @elseif ($offer->transaction && $offer->transaction->status === 'completed' && !$offer->transaction->reviews()->where('reviewer_id', Auth::id())->exists())
                                        <x-review-modal :transaction="$offer->transaction" :recipientName="$offer->item->user->name" />
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Section Ventes terminées (pour le vendeur) --}}
            @php
                $completedSales = $items->filter(function ($item) {
                    return $item->status === \App\Enums\ItemStatus::SOLD && $item->offers->where('status', 'paid')->contains(function ($offer) {
                        return $offer->transaction && $offer->transaction->status === 'completed';
                    });
                });
            @endphp
            @if ($completedSales->isNotEmpty())
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-2xl font-bold mb-6 text-center sm:text-left">Mes ventes terminées</h3>
                        <div class="space-y-4">
                            @foreach ($completedSales as $item)
                                @php
                                    $completedTransaction = $item->offers->where('status', 'paid')->first()->transaction;
                                    $buyer = $completedTransaction->offer->user;
                                @endphp
                                <div class="p-4 border rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between">
                                    <div class="flex items-center">
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
                                    </div>
                                    <div class="mt-4 sm:mt-0">
                                        @if (!$completedTransaction->reviews()->where('reviewer_id', Auth::id())->exists())
                                            <x-review-modal :transaction="$completedTransaction" :recipientName="$buyer->name" />
                                        @else
                                            <p class="text-sm text-gray-500">Avis déjà laissé.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-status-btn').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const itemId = this.dataset.itemId;
                const url = `/api/items/${itemId}/toggle-status`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                })
                .then(response => response.json())
                .then(data => {
                    // Mettre à jour le badge de statut
                    const statusBadge = document.getElementById(`status-badge-${itemId}`);
                    statusBadge.textContent = data.newStatusText;
                    statusBadge.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'; // Reset classes
                    if (data.isAvailable) {
                        statusBadge.classList.add('bg-green-100', 'text-green-800');
                    } else {
                        statusBadge.classList.add('bg-gray-100', 'text-gray-800');
                    }

                    // Mettre à jour le bouton
                    this.textContent = data.isAvailable ? 'Dépublier' : 'Publier';
                })
                .catch(error => console.error('Erreur:', error));
            });
        });
    });
    </script>
    @endpush
</x-app-layout>
