<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détail du Litige #') }}{{ $dispute->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- Colonne principale avec les détails et la conversation -->
            <div class="md:col-span-2 space-y-8">
                <!-- Détails du litige -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Raison du litige</h3>
                        <p class="text-gray-700">{{ $dispute->reason }}</p>
                        <p class="text-sm text-gray-500 mt-2">Ouvert par {{ $dispute->user->name }} le {{ $dispute->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                <!-- Conversation -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Conversation entre le vendeur et l'acheteur</h3>
                        @if($conversation && $conversation->messages->count() > 0)
                            <div class="space-y-4 max-h-96 overflow-y-auto">
                                @foreach($conversation->messages as $message)
                                    <div class="p-3 rounded-lg {{ $message->user_id === $buyer->id ? 'bg-blue-100' : 'bg-gray-100' }}">
                                        <p class="font-semibold">{{ $message->user->name }}</p>
                                        <p>{{ $message->content }}</p>
                                        <p class="text-xs text-gray-500 text-right">{{ $message->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">Aucune conversation trouvée pour cette transaction.</p>
                        @endif
                    </div>
                </div>

                <!-- Actions de l'administrateur -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                        @if($dispute->status === 'open')
                            <div class="flex space-x-4">
                                <form action="{{ route('admin.disputes.resolveForBuyer', $dispute) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir rembourser l'acheteur ? Cette action est irréversible.');">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Rembourser l'acheteur</button>
                                </form>
                                <form action="{{ route('admin.disputes.resolveForSeller', $dispute) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir donner raison au vendeur ? Cette action est irréversible.');">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Donner raison au vendeur</button>
                                </form>
                                <form action="{{ route('admin.disputes.close', $dispute) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir clore ce litige sans action financière ?');">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Clore le litige</button>
                                </form>
                            </div>
                        @else
                            <p class="text-gray-500">Ce litige a été clôturé le {{ $dispute->updated_at->format('d/m/Y') }}.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne latérale avec les informations sur les utilisateurs et la transaction -->
            <div class="space-y-8">
                <!-- Détails de la transaction -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Transaction #{{ $transaction->id }}</h3>
                        <p><strong>Article :</strong> {{ $item->title }}</p>
                        <p><strong>Montant :</strong> {{ number_format($transaction->amount, 2, ',', ' ') }} €</p>
                        <p><strong>Date :</strong> {{ $transaction->created_at->format('d/m/Y') }}</p>
                        <p><strong>Statut :</strong> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $transaction->status->value }}</span></p>
                    </div>
                </div>

                <!-- Informations sur le vendeur -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Vendeur</h3>
                        <p><strong>Nom :</strong> {{ $seller->name }}</p>
                        <p><strong>Email :</strong> {{ $seller->email }}</p>
                        <a href="{{ route('admin.users.index', ['search' => $seller->email]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Voir l'utilisateur</a>
                    </div>
                </div>

                <!-- Informations sur l'acheteur -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Acheteur</h3>
                        <p><strong>Nom :</strong> {{ $buyer->name }}</p>
                        <p><strong>Email :</strong> {{ $buyer->email }}</p>
                        <a href="{{ route('admin.users.index', ['search' => $buyer->email]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Voir l'utilisateur</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
