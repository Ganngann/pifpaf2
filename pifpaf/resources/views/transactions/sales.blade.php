<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Ventes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @forelse ($sales as $transaction)
                        <div class="mb-6">
                            <x-sales.card :transaction="$transaction" />
                        </div>
                    @empty
                        <x-ui.empty-state>
                            Vous n'avez réalisé aucune vente pour le moment.
                        </x-ui.empty-state>
                    @endforelse

                    <div class="mt-8">
                        {{ $sales->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
