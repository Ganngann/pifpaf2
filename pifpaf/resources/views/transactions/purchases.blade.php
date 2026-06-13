<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Achats') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 space-y-4">
                    @forelse ($purchases as $transaction)
                        <x-purchase-card :transaction="$transaction" />
                    @empty
                        <x-ui.empty-state>
                            Vous n'avez effectué aucun achat pour le moment.
                            <x-slot name="actions">
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 transition ease-in-out duration-150">
                                    Découvrir les articles
                                </a>
                            </x-slot>
                        </x-ui.empty-state>
                    @endforelse

                    @if ($purchases->hasPages())
                        <div class="mt-4">
                            {{ $purchases->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
