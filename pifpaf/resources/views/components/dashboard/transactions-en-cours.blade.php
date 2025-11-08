@props(['openTransactions'])

<div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <h3 class="text-2xl font-bold mb-6 text-center sm:text-left">Transactions en cours</h3>
        @if ($openTransactions->isEmpty())
            <div class="text-center text-gray-500">
                <p>Vous n'avez aucune transaction en cours.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($openTransactions as $transaction)
                    @php
                        $viewpoint = ($transaction->offer->user_id === Auth::id()) ? 'buyer' : 'seller';
                    @endphp
                    <x-dashboard.transaction-card :transaction="$transaction" :viewpoint="$viewpoint" />
                @endforeach
            </div>
        @endif
    </div>
</div>
