<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Portefeuille') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Solde actuel</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-700">
                        {{ number_format(Auth::user()->wallet, 2, ',', ' ') }} €
                    </p>
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Demander un virement</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Cette fonctionnalité n'est pas encore disponible. Elle vous permettra de transférer le solde de votre portefeuille vers votre compte bancaire.
                    </p>
                    <button class="mt-4 inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest cursor-not-allowed" disabled>
                        Demander un virement
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
