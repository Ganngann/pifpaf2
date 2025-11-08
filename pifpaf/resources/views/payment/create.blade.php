<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paiement de l\'offre') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200"
                     x-data="{
                        totalAmount: {{ $offer->amount }},
                        walletBalance: {{ $walletBalance }},
                        useWallet: false,
                        get walletAmount() {
                            return this.useWallet ? Math.min(this.totalAmount, this.walletBalance) : 0;
                        },
                        get cardAmount() {
                            return this.totalAmount - this.walletAmount;
                        }
                     }">

                    <h3 class="text-2xl font-bold mb-6">Récapitulatif de la commande</h3>

                    <div class="mb-6 border-b pb-4">
                        <p><span class="font-semibold">Article :</span> {{ $offer->item->title }}</p>
                        <p class="text-lg"><span class="font-semibold">Montant total :</span> <span x-text="totalAmount.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' })"></span></p>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-xl font-bold mb-4">Mode de paiement</h4>
                        <div class="p-4 rounded-lg bg-gray-50 border">
                            <p class="font-semibold">Votre portefeuille : <span class="text-green-600 font-bold">{{ number_format($walletBalance, 2, ',', ' ') }} €</span></p>
                            @if ($walletBalance > 0)
                                <div class="mt-2">
                                    <label for="use_wallet" class="inline-flex items-center">
                                        <input id="use_wallet" type="checkbox" x-model="useWallet" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">Utiliser mon solde pour cet achat</span>
                                    </label>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 mt-1">Votre solde est insuffisant pour être utilisé.</p>
                            @endif
                        </div>
                    </div>


                    <form action="{{ route('payment.store', $offer) }}" method="POST">
                        @csrf
                        <input type="hidden" name="use_wallet" :value="useWallet">
                        <input type="hidden" name="wallet_amount" :value="walletAmount">
                        <input type="hidden" name="card_amount" :value="cardAmount">

                        <div x-show="cardAmount > 0" class="space-y-4 border-t pt-6">
                             <p class="font-semibold">Paiement par carte : <span class="text-blue-600 font-bold" x-text="cardAmount.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' })"></span></p>
                            {{-- Numéro de carte --}}
                            <div>
                                <label for="card_number" class="block font-medium text-sm text-gray-700">Numéro de carte de crédit</label>
                                <input id="card_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="1234 5678 9101 1121" :required="cardAmount > 0">
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                {{-- Date d'expiration --}}
                                <div>
                                    <label for="expiry_date" class="block font-medium text-sm text-gray-700">Date d'expiration</label>
                                    <input id="expiry_date" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="MM/AA" :required="cardAmount > 0">
                                </div>

                                {{-- CVC --}}
                                <div>
                                    <label for="cvc" class="block font-medium text-sm text-gray-700">CVC</label>
                                    <input id="cvc" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="123" :required="cardAmount > 0">
                                </div>
                            </div>
                        </div>

                        <div x-show="cardAmount == 0 && useWallet" class="text-center p-4 bg-green-100 text-green-800 rounded-lg">
                            <p>Le montant total sera débité de votre portefeuille.</p>
                        </div>

                        <div class="mt-6 border-t pt-6">
                             <div class="text-lg font-bold mb-4">
                                <div class="flex justify-between">
                                    <span>Total à payer :</span>
                                    <span x-text="totalAmount.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' })"></span>
                                </div>
                                <div class="flex justify-between text-sm font-normal text-green-600" x-show="useWallet">
                                     <span>Déduit du portefeuille :</span>
                                     <span x-text="'-' + walletAmount.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' })"></span>
                                </div>
                                 <div class="flex justify-between font-bold border-t mt-2 pt-2">
                                     <span>Reste à payer :</span>
                                     <span x-text="cardAmount.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' })"></span>
                                 </div>
                            </div>
                            <button type="submit" dusk="submit-payment-button" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 disabled:opacity-50" :disabled="totalAmount <= 0 && !useWallet">
                               <span x-show="cardAmount > 0">Payer</span>
                               <span x-show="cardAmount == 0 && useWallet">Confirmer la commande</span>
                               <span x-show="cardAmount > 0" x-text="cardAmount.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' })"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
