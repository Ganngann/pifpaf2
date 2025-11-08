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

            <div class="space-y-6">
                <x-ui.card>
                    <div class="p-6 border-b border-gray-200">
                        @if($items->isEmpty())
                            <x-dashboard.empty-dashboard />
                        @else
                            <x-dashboard.annonces-list :items="$items" />
                        @endif
                    </div>
                </x-ui.card>

                {{-- Section Ventes à retirer --}}
                @if ($soldItemsForPickup->isNotEmpty())
                    <x-ui.card>
                        <x-dashboard.ventes-a-retirer :soldItemsForPickup="$soldItemsForPickup" />
                    </x-ui.card>
                @endif

                {{-- Section Transactions en cours --}}
                <x-ui.card>
                    <x-dashboard.transactions-en-cours :openTransactions="$openTransactions" />
                </x-ui.card>

                {{-- Section Ventes terminées (pour le vendeur) --}}
                @if ($completedSales->isNotEmpty())
                    <x-ui.card>
                        <x-dashboard.dernieres-ventes :completedSales="$completedSales" />
                    </x-ui.card>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
