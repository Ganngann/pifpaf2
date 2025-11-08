<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Adresses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Toutes mes adresses</h3>
                        <a href="{{ route('profile.addresses.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Ajouter une adresse
                        </a>
                    </div>
                    @if($addresses->isEmpty())
                        <p>Vous n'avez pas encore d'adresse enregistrée.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($addresses as $address)
                                <div class="p-4 border rounded-lg flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold">{{ $address->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->street }}, {{ $address->postal_code }} {{ $address->city }}, {{ $address->country }}</p>
                                        <div class="mt-2 flex space-x-2">
                                            @if($address->is_for_pickup)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Retrait
                                                </span>
                                            @endif
                                            @if($address->is_for_delivery)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Livraison
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('profile.addresses.edit', $address) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                        <form action="{{ route('profile.addresses.destroy', $address) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette adresse ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
