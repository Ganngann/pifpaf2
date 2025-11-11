<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier les informations bancaires') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('profile.bank-accounts.update', $bankAccount) }}">
                        @csrf
                        @method('PUT')

                        <!-- Account Holder Name -->
                        <div>
                            <x-input-label for="account_holder_name" :value="__('Nom du titulaire du compte')" />
                            <x-text-input id="account_holder_name" class="block mt-1 w-full" type="text" name="account_holder_name" :value="old('account_holder_name', $bankAccount->account_holder_name)" required autofocus />
                            <x-input-error :messages="$errors->get('account_holder_name')" class="mt-2" />
                        </div>

                        <!-- IBAN -->
                        <div class="mt-4">
                            <x-input-label for="iban" :value="__('IBAN')" />
                            <x-text-input id="iban" class="block mt-1 w-full" type="text" name="iban" :value="old('iban', $bankAccount->iban)" required />
                            <x-input-error :messages="$errors->get('iban')" class="mt-2" />
                        </div>

                        <!-- BIC -->
                        <div class="mt-4">
                            <x-input-label for="bic" :value="__('BIC / SWIFT')" />
                            <x-text-input id="bic" class="block mt-1 w-full" type="text" name="bic" :value="old('bic', $bankAccount->bic)" required />
                            <x-input-error :messages="$errors->get('bic')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('profile.bank-accounts.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Annuler') }}
                            </a>
                            <x-primary-button>
                                {{ __('Mettre à jour') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
