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

                        <x-admin.stat-card
                            title="Utilisateurs"
                            :count="$userCount"
                            :link="route('admin.users.index')"
                            linkText="Gérer les utilisateurs"
                            color="blue"
                        />

                        <x-admin.stat-card
                            title="Annonces"
                            :count="$itemCount"
                            :link="route('admin.items.index')"
                            linkText="Gérer les annonces"
                            color="green"
                        />

                        <x-admin.stat-card
                            title="Transactions"
                            :count="$transactionCount"
                            link="#"
                            linkText="Voir les transactions"
                            color="yellow"
                        />

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
