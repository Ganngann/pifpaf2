<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Adresses de Retrait') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('profile.addresses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Ajouter une nouvelle adresse
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse ($addresses as $address)
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
            </div>
        </div>
    </div>
</x-app-layout>
