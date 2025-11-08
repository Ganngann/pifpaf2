<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Portefeuille') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Section Solde Actuel -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Solde actuel</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-700">
                        {{ number_format($user->wallet, 2, ',', ' ') }} €
                    </p>
                </div>
            </div>

            <!-- Section Demander un virement -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Demander un virement</h3>
                    <form action="{{ route('wallet.withdraw') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="flex items-center">
                            <input type="number" name="amount" id="amount" step="0.01" min="1" max="{{ $user->wallet }}" class="form-input rounded-md shadow-sm mt-1 block" placeholder="Montant" required>
                            <button type="submit" class="ml-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 disabled:opacity-25 transition">
                                Retirer
                            </button>
                        </div>
                        @error('amount')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>

            <!-- Section Historique des opérations -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historique des opérations</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Montant
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($walletHistories as $history)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $history->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if ($history->transaction)
                                                <a href="{{ route('transactions.show', $history->transaction) }}" class="text-indigo-600 hover:text-indigo-900 underline">
                                                    {{ $history->description }}
                                                </a>
                                            @else
                                                {{ $history->description }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($history->type === 'credit')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Crédit
                                                </span>
                                            @elseif ($history->type === 'debit')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Débit
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Retrait
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium
                                            @if ($history->type === 'credit') text-green-600 @else text-red-600 @endif">
                                            {{ ($history->type === 'credit' ? '+' : '-') . number_format($history->amount, 2, ',', ' ') }} €
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            Aucune opération pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
