<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tableau de bord Vendeur') }}
            </h2>
            <a href="{{ route('items.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                {{ __('Créer une annonce') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('success')" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vos Annonces</h3>
                    @if($items->isEmpty())
                        <p>Vous n'avez aucune annonce pour le moment.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach ($items as $item)
                                <div class="border rounded-lg p-4 flex flex-col">
                                    <div class="flex-grow">
                                        <div class="font-bold text-lg">{{ $item->title }}</div>
                                        <div class="text-gray-600">{{ number_format($item->price, 2, ',', ' ') }} €</div>
                                    </div>
                                    <div class="mt-4 flex justify-end space-x-4">
                                        <a href="{{ route('items.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                        <form class="inline-block" action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">
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
