<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Informations Bancaires') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Vos comptes bancaires</h3>
                        @if($bankAccounts->isNotEmpty())
                            <a href="{{ route('profile.bank-accounts.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Ajouter un compte') }}
                            </a>
                        @endif
                    </div>

                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mt-6">
                        @if ($bankAccounts->isEmpty())
                            <x-ui.empty-state>
                                <x-slot name="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mx-auto">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                    </svg>
                                </x-slot>
                                Vous n'avez pas encore de compte bancaire enregistré.
                                <x-slot name="actions">
                                    <a href="{{ route('profile.bank-accounts.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Ajouter un compte') }}
                                    </a>
                                </x-slot>
                            </x-ui.empty-state>
                        @else
                            <ul>
                                @foreach ($bankAccounts as $account)
                                    <li class="border-b py-4 flex justify-between items-center">
                                        <div>
                                            <p class="font-semibold">{{ $account->account_holder_name }}</p>
                                            <p class="text-sm text-gray-600">IBAN: {{ $account->iban }} | BIC: {{ $account->bic }}</p>
                                        </div>
                                        <div class="flex items-center">
                                            <a href="{{ route('profile.bank-accounts.edit', $account) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Modifier</a>
                                            <form action="{{ route('profile.bank-accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
