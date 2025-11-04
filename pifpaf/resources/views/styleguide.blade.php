<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Styleguide des Composants') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">

            {{-- Helper function to display component titles --}}
            @php
            function component_title($title) {
                echo '<h3 class="text-2xl font-bold mb-4 border-b pb-2">' . e($title) . '</h3>';
            }
            @endphp

            <!-- Section Atomic Components -->
            <div>
                {!! component_title('1. Composants Atomiques') !!}
                <div class="space-y-8">
                    <!-- Status Badge -->
                    <div>
                        <h4 class="text-lg font-semibold mb-2">ui.status-badge</h4>
                        <div class="flex items-center space-x-4 p-4 bg-gray-100 rounded-lg">
                            <x-ui.status-badge status="{{ \App\Enums\ItemStatus::AVAILABLE }}" />
                            <x-ui.status-badge status="{{ \App\Enums\ItemStatus::UNPUBLISHED }}" />
                            <x-ui.status-badge status="{{ \App\Enums\ItemStatus::SOLD }}" />
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div>
                        <h4 class="text-lg font-semibold mb-2">ui.empty-state</h4>
                        <div class="p-4 bg-gray-100 rounded-lg">
                            <x-ui.empty-state>
                                Aucun résultat trouvé
                                <x-slot name="description">Essayez d'ajuster vos filtres de recherche.</x-slot>
                            </x-ui.empty-state>
                        </div>
                    </div>

                    <!-- Item Thumbnail -->
                    <div>
                        <h4 class="text-lg font-semibold mb-2">ui.item-thumbnail</h4>
                        <div class="flex space-x-4 p-4 bg-gray-100 rounded-lg">
                            @if($itemWithImage)
                                <div>
                                    <p class="mb-1 text-sm">Avec image :</p>
                                    <x-ui.item-thumbnail :item="$itemWithImage" class="w-24 h-24 rounded-md shadow-md" />
                                </div>
                            @endif
                            @if($itemWithoutImage)
                                <div>
                                    <p class="mb-1 text-sm">Sans image (fallback) :</p>
                                    <x-ui.item-thumbnail :item="$itemWithoutImage" class="w-24 h-24 rounded-md shadow-md" />
                                </div>
                            @endif
                        </div>
                    </div>

                     <!-- User Profile Link -->
                    <div>
                        <h4 class="text-lg font-semibold mb-2">ui.user-profile-link</h4>
                        <div class="p-4 bg-gray-100 rounded-lg">
                            @if($testUser)
                               <x-ui.user-profile-link :user="$testUser" />
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <!-- Section Molecular Components -->
            <div>
                {!! component_title('2. Composants Moléculaires') !!}
                <div class="space-y-8">
                    <!-- Dashboard Item Card -->
                    <div>
                        <h4 class="text-lg font-semibold mb-2">dashboard.item-card</h4>
                        <div class="p-4 bg-gray-100 rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4">
                           @if($itemWithImage)
                                <x-dashboard.item-card :item="$itemWithImage" />
                                <x-dashboard.item-card :item="$itemWithImage">
                                    <x-slot name="actions">
                                        <button class="text-sm bg-blue-500 text-white px-2 py-1 rounded">Action 1</button>
                                        <button class="text-sm bg-gray-500 text-white px-2 py-1 rounded">Action 2</button>
                                    </x-slot>
                                </x-dashboard.item-card>
                           @endif
                        </div>
                    </div>

                    <!-- UI Item Card -->
                    <div>
                        <h4 class="text-lg font-semibold mb-2">ui.item-card (Public)</h4>
                         <div class="p-4 bg-gray-100 rounded-lg">
                            <div class="max-w-xs">
                                @if($itemWithImage)
                                    <x-ui.item-card :item="$itemWithImage" />
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- UI Offer Card -->
                    <div>
                        <h4 class="text-lg font-semibold mb-2">ui.offer-card</h4>
                         <div class="p-4 bg-gray-100 rounded-lg space-y-4">
                             @if($offerPending)
                                <div>
                                    <p class="text-sm mb-1">Vue vendeur (offre en attente) :</p>
                                    <x-ui.offer-card :offer="$offerPending" viewpoint="seller">
                                        <x-slot name="actions">
                                            <button class="text-xs bg-green-500 text-white px-2 py-1 rounded">Accepter</button>
                                            <button class="text-xs bg-red-500 text-white px-2 py-1 rounded">Refuser</button>
                                        </x-slot>
                                    </x-ui.offer-card>
                                </div>
                             @endif
                             @if($offerAccepted)
                                <div>
                                    <p class="text-sm mb-1">Vue acheteur (offre acceptée) :</p>
                                    <x-ui.offer-card :offer="$offerAccepted" viewpoint="buyer">
                                         <x-slot name="actions">
                                            <button class="text-sm bg-green-600 text-white px-3 py-1 rounded">Payer</button>
                                        </x-slot>
                                    </x-ui.offer-card>
                                </div>
                             @endif
                        </div>
                    </div>

                    <!-- UI Review Card -->
                    <div>
                        <h4 class="text-lg font-semibold mb-2">ui.review-card</h4>
                         <div class="p-4 bg-gray-100 rounded-lg">
                            @if($review)
                                <x-ui.review-card :review="$review" />
                            @endif
                        </div>
                    </div>

                    <!-- UI Wallet History Card -->
                    <div>
                        <h4 class="text-lg font-semibold mb-2">ui.wallet-history-card</h4>
                         <div class="p-4 bg-gray-100 rounded-lg space-y-4">
                            @if($walletCredit)
                                <x-ui.wallet-history-card :history="$walletCredit" />
                            @endif
                            @if($walletDebit)
                                <x-ui.wallet-history-card :history="$walletDebit" />
                            @endif
                             @if($walletWithdrawal)
                                <x-ui.wallet-history-card :history="$walletWithdrawal" />
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
