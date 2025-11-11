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
                        <a href="{{ route('profile.bank-accounts.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Ajouter un compte') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mt-6">
                        @if ($bankAccounts->isEmpty())
                            <p>Vous n'avez pas encore de compte bancaire enregistré.</p>
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
