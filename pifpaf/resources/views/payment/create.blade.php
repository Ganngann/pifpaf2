<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paiement de l\'offre') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" x-data="{
                    useWallet: false,
                    offerAmount: {{ $offer->amount }},
                    walletBalance: {{ $walletBalance }},
                    get amountToPay() {
                        let payAmount = this.useWallet ? this.offerAmount - this.walletBalance : this.offerAmount;
                        return Math.max(0, payAmount).toFixed(2);
                    },
                    get walletCoversAll() {
                        return this.useWallet && this.walletBalance >= this.offerAmount;
                    },
                    formatCurrency(value) {
                        return new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value).replace(',', '.') + ' €';
                    }
                }">
                    <h3 class="text-2xl font-bold mb-6">Récapitulatif de la commande</h3>

                    <div class="mb-6 border-b pb-6">
                        <p class="text-lg"><span class="font-semibold">Article :</span> {{ $offer->item->title }}</p>
                        <p class="text-lg"><span class="font-semibold">Montant de l'offre :</span> {{ number_format($offer->amount, 2, ',', ' ') }} €</p>
                    </div>

                    <form action="{{ route('payment.store', $offer) }}" method="POST">
                        @csrf

                        @if ($walletBalance > 0)
                        <div class="mb-6">
                            <label for="use_wallet" class="flex items-center text-lg">
                                <input id="use_wallet" name="use_wallet" type="checkbox" x-model="useWallet" class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-3 font-semibold text-gray-700">
                                    Utiliser mon solde de portefeuille
                                    <span class="font-bold text-indigo-600">({{ number_format($walletBalance, 2, ',', ' ') }} €)</span>
                                </span>
                            </label>
                            <p class="mt-2 text-sm text-gray-600" x-show="useWallet">
                                Montant déduit : <span class="font-semibold" x-text="formatCurrency(Math.min(offerAmount, walletBalance))"></span>
                            </p>
                        </div>
                        @endif

                        <div class="text-xl font-bold mb-6">
                            <p>Total à payer : <span x-text="formatCurrency(amountToPay)"></span></p>
                        </div>


                        {{-- Formulaire de paiement par carte --}}
                        <div x-show="!walletCoversAll" class="space-y-4">
                             <h4 class="text-xl font-semibold mb-4">Paiement par carte</h4>
                            {{-- Numéro de carte --}}
                            <div>
                                <label for="card_number" class="block font-medium text-sm text-gray-700">Numéro de carte de crédit</label>
                                <input id="card_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="1234 5678 9101 1121" :required="!walletCoversAll">
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                {{-- Date d'expiration --}}
                                <div>
                                    <label for="expiry_date" class="block font-medium text-sm text-gray-700">Date d'expiration</label>
                                    <input id="expiry_date" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="MM/AA" :required="!walletCoversAll">
                                </div>

                                {{-- CVC --}}
                                <div>
                                    <label for="cvc" class="block font-medium text-sm text-gray-700">CVC</label>
                                    <input id="cvc" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="123" :required="!walletCoversAll">
                                </div>
                            </div>
                        </div>

                         <div class="mt-6" x-show="walletCoversAll">
                            <p class="text-green-600 font-semibold">Votre solde de portefeuille couvre la totalité de la commande.</p>
                        </div>


                        <div class="mt-6">
                            <button type="submit" dusk="submit-payment-button" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                                <span x-show="!walletCoversAll">Payer </span>
                                <span x-show="walletCoversAll">Confirmer et payer avec mon solde</span>
                                <span x-text="formatCurrency(amountToPay)"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
