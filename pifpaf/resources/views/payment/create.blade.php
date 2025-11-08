<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paiement de l\'offre') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-2xl font-bold mb-6">Récapitulatif de la commande</h3>

                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 bg-gray-300 rounded-lg overflow-hidden">
                            <img src="{{ asset('storage/' . optional($offer->item->primaryImage)->image_path) }}" alt="{{ $offer->item->title }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-semibold text-lg">{{ $offer->item->title }}</p>
                            <p class="text-gray-600">Offre : {{ number_format($offer->amount, 2, ',', ' ') }} €</p>
                        </div>
                    </div>
                    {{-- Simulation d'un formulaire de paiement --}}
                    <form action="{{ route('payment.store', $offer) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            {{-- Numéro de carte --}}
                            <div>
                                <label for="card_number" class="block font-medium text-sm text-gray-700">Numéro de carte de crédit</label>
                                <input id="card_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="1234 5678 9101 1121" required>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                {{-- Date d'expiration --}}
                                <div>
                                    <label for="expiry_date" class="block font-medium text-sm text-gray-700">Date d'expiration</label>
                                    <input id="expiry_date" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="MM/AA" required>
                                </div>

                                {{-- CVC --}}
                                <div>
                                    <label for="cvc" class="block font-medium text-sm text-gray-700">CVC</label>
                                    <input id="cvc" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="123" required>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" dusk="submit-payment-button" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                                Payer {{ number_format($offer->amount, 2, ',', ' ') }} €
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
