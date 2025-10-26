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
                                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                                    <div class="p-4 flex flex-col flex-grow">
                                        <h4 class="text-xl font-semibold">{{ $item->title }}</h4>
                                        <p class="text-gray-700 mt-2 flex-grow">{{ Str::limit($item->description, 100) }}</p>
                                        <p class="text-lg font-bold text-gray-900 mt-4">{{ number_format($item->price, 2, ',', ' ') }} €</p>
                                        <div class="mt-4 flex justify-end space-x-2">
                                            <a href="{{ route('items.edit', $item) }}" class="text-sm bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Modifier</a>
                                            <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Supprimer</button>
                                            </form>
                                        </div>
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
