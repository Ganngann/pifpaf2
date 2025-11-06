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
                        <x-dashboard.empty-dashboard />
                    @else
                        <x-dashboard.annonces-list :items="$items" />
                    @endif
                </div>
            </div>

            {{-- Section Ventes à retirer --}}
            @if ($soldItemsForPickup->isNotEmpty())
                <x-dashboard.ventes-a-retirer :soldItemsForPickup="$soldItemsForPickup" />
            @endif

            {{-- Section Transactions en cours --}}
            <x-dashboard.transactions-en-cours :openTransactions="$openTransactions" />

            {{-- Section Ventes terminées (pour le vendeur) --}}
            @if ($completedSales->isNotEmpty())
                <x-dashboard.dernieres-ventes :completedSales="$completedSales" />
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
