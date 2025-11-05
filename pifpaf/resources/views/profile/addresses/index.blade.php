<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Adresses') }}
        </h2>
    </x-slot>

    <div x-data="{ tab: 'pickup' }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Tabs -->
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button @click="tab = 'pickup'"
                                :class="{ 'border-indigo-500 text-indigo-600': tab === 'pickup', 'border-transparent hover:text-gray-600 hover:border-gray-300': tab !== 'pickup' }"
                                class="inline-block p-4 border-b-2 rounded-t-lg"
                                type="button">Adresses de retrait</button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button @click="tab = 'shipping'"
                                :class="{ 'border-indigo-500 text-indigo-600': tab === 'shipping', 'border-transparent hover:text-gray-600 hover:border-gray-300': tab !== 'shipping' }"
                                class="inline-block p-4 border-b-2 rounded-t-lg"
                                type="button">Adresses de livraison</button>
                    </li>
                </ul>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Contenu de l'onglet Adresses de retrait -->
                    <div x-show="tab === 'pickup'" id="pickup-addresses">
                        <div class="flex justify-end mb-4">
                            <a href="{{ route('profile.addresses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Ajouter une adresse de retrait
                            </a>
                        </div>
                        <div class="space-y-4">
                            @forelse ($pickupAddresses as $address)
                                <div class="p-4 border rounded-lg flex justify-between items-center">
                                    <div>
                                        <p class="font-bold">{{ $address->name }}</p>
                                        <p>{{ $address->street }}, {{ $address->city }}, {{ $address->postal_code }}</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('profile.addresses.edit', $address) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Modifier</a>
                                    </div>
                                </div>
                            @empty
                                <p>Vous n'avez aucune adresse de retrait pour le moment.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Contenu de l'onglet Adresses de livraison -->
                    <div x-show="tab === 'shipping'" id="shipping-addresses" style="display: none;">
                        <div class="flex justify-end mb-4">
                            <a href="{{ route('profile.shipping_addresses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Ajouter une adresse de livraison
                            </a>
                        </div>
                        <div class="space-y-4">
                            @forelse ($shippingAddresses as $address)
                                <div class="p-4 border rounded-lg flex justify-between items-center">
                                    <div>
                                        <p class="font-bold">{{ $address->name }}</p>
                                        <p>{{ $address->street }}, {{ $address->postal_code }} {{ $address->city }}, {{ $address->country }}</p>
                                    </div>
                                    <div class="flex items-center">
                                        <a href="{{ route('profile.shipping_addresses.edit', $address) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Modifier</a>
                                        <form action="{{ route('profile.shipping_addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette adresse ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p>Vous n'avez aucune adresse de livraison pour le moment.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
