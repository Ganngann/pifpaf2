<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord Administrateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques Clés</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Statistique Utilisateurs -->
                        <div class="bg-blue-100 p-6 rounded-lg">
                            <h4 class="text-lg font-semibold text-blue-800">Utilisateurs</h4>
                            <p class="text-3xl font-bold text-blue-900 mt-2">{{ $userCount }}</p>
                            <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:underline mt-4 inline-block">Gérer les utilisateurs</a>
                        </div>

                        <!-- Statistique Annonces -->
                        <div class="bg-green-100 p-6 rounded-lg">
                            <h4 class="text-lg font-semibold text-green-800">Annonces</h4>
                            <p class="text-3xl font-bold text-green-900 mt-2">{{ $itemCount }}</p>
                            <a href="#" class="text-green-600 hover:underline mt-4 inline-block">Gérer les annonces</a>
                        </div>

                        <!-- Statistique Transactions -->
                        <div class="bg-yellow-100 p-6 rounded-lg">
                            <h4 class="text-lg font-semibold text-yellow-800">Transactions</h4>
                            <p class="text-3xl font-bold text-yellow-900 mt-2">{{ $transactionCount }}</p>
                            <a href="#" class="text-yellow-600 hover:underline mt-4 inline-block">Voir les transactions</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
