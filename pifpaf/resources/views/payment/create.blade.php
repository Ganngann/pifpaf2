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

                    <form id="payment-form" action="{{ route('payment.store', $offer) }}" method="POST" x-data="paymentForm" @submit.prevent="handleSubmit">
                        @csrf
                        <input type="hidden" name="payment_intent_id" x-ref="payment_intent_id">

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


                        {{-- Conteneur pour Stripe Elements --}}
                        <div x-show="!walletCoversAll">
                            <h4 class="text-xl font-semibold mb-4">Paiement par carte</h4>
                            <div id="card-element" class="p-4 border rounded-md shadow-sm">
                                <!-- L'élément Stripe Card sera injecté ici -->
                            </div>
                            <div id="card-errors" role="alert" class="text-red-600 mt-2 text-sm"></div>
                        </div>

                        <div class="mt-6" x-show="walletCoversAll">
                           <p class="text-green-600 font-semibold">Votre solde de portefeuille couvre la totalité de la commande.</p>
                        </div>

                        <div class="mt-8">
                            <button type="submit"
                                    dusk="submit-payment-button"
                                    class="w-full bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                    :disabled="loading">
                                <span x-show="loading" class="animate-spin mr-2">Processing...</span>
                                <span x-show="!loading">
                                    <span x-show="!walletCoversAll">Payer</span>
                                    <span x-show="walletCoversAll">Confirmer et payer avec le solde</span>
                                    <span x-text="formatCurrency(amountToPay)"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('paymentForm', () => ({
                loading: false,
                stripe: null,
                cardElement: null,
                clientSecret: '{{ $intent ? $intent->client_secret : null }}',

                init() {
                    if (this.clientSecret) {
                        this.stripe = Stripe('{{ config('services.stripe.key') }}');
                        const elements = this.stripe.elements();
                        this.cardElement = elements.create('card', {
                            style: {
                                base: {
                                    fontSize: '16px',
                                    color: '#32325d',
                                }
                            }
                        });
                        this.cardElement.mount('#card-element');

                        this.cardElement.on('change', (event) => {
                            const displayError = document.getElementById('card-errors');
                            if (event.error) {
                                displayError.textContent = event.error.message;
                            } else {
                                displayError.textContent = '';
                            }
                        });
                    }
                },

                async handleSubmit(event) {
                    event.preventDefault();
                    this.loading = true;

                    const { useWallet, walletBalance, offerAmount } = this.$data;
                    const walletCoversAll = useWallet && walletBalance >= offerAmount;

                    // Si le portefeuille couvre tout, on soumet le formulaire directement
                    if (walletCoversAll) {
                        event.target.submit();
                        return;
                    }

                    // Si le paiement par carte est nécessaire mais que l'intention n'a pas pu être créée
                    if (!this.clientSecret) {
                        alert("Le montant à payer par carte est trop faible pour être traité.");
                        this.loading = false;
                        return;
                    }

                    // Confirmer le paiement de la carte avec Stripe
                    const { paymentIntent, error } = await this.stripe.confirmCardPayment(
                        this.clientSecret, {
                            payment_method: {
                                card: this.cardElement,
                                billing_details: {
                                    // Idéalement, on ajouterait le nom et l'email de l'utilisateur ici
                                    name: '{{ auth()->user()->name }}',
                                    email: '{{ auth()->user()->email }}',
                                }
                            }
                        }
                    );

                    if (error) {
                        // Afficher l'erreur à l'utilisateur
                        const errorElement = document.getElementById('card-errors');
                        errorElement.textContent = error.message;
                        this.loading = false;
                    } else {
                        // Le paiement a réussi
                        this.$refs.payment_intent_id.value = paymentIntent.id;
                        event.target.submit();
                    }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
