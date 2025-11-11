<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Demandes de Virement') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Infos Bancaires</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($withdrawalRequests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($request->amount, 2, ',', ' ') }} €</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $request->bankAccount->account_holder_name }}<br>
                                            IBAN: {{ $request->bankAccount->iban }}<br>
                                            BIC: {{ $request->bankAccount->bic }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @switch($request->status)
                                                    @case(\App\Enums\WithdrawalRequestStatus::PENDING) bg-yellow-100 text-yellow-800 @break
                                                    @case(\App\Enums\WithdrawalRequestStatus::APPROVED) bg-blue-100 text-blue-800 @break
                                                    @case(\App\Enums\WithdrawalRequestStatus::PAID) bg-green-100 text-green-800 @break
                                                    @case(\App\Enums\WithdrawalRequestStatus::REJECTED)
                                                    @case(\App\Enums\WithdrawalRequestStatus::FAILED) bg-red-100 text-red-800 @break
                                                @endswitch
                                            ">
                                                {{ $request->status->value }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                @if ($request->status === \App\Enums\WithdrawalRequestStatus::PENDING)
                                                    <form action="{{ route('admin.withdrawal-requests.approve', $request) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900">Approuver</button>
                                                    </form>
                                                    <form action="{{ route('admin.withdrawal-requests.reject', $request) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Rejeter</button>
                                                    </form>
                                                @elseif ($request->status === \App\Enums\WithdrawalRequestStatus::APPROVED)
                                                    <form action="{{ route('admin.withdrawal-requests.pay', $request) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900">Marquer Payé</button>
                                                    </form>
                                                    <form action="{{ route('admin.withdrawal-requests.fail', $request) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="text-red-600 hover:text-red-900">Marquer Échoué</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            Aucune demande de virement.
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
