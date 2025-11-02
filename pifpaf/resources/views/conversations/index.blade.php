<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messagerie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-2xl font-bold mb-6">Vos Conversations</h3>

                    @if($conversations->isEmpty())
                        <p>Vous n'avez aucune conversation pour le moment.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($conversations as $conversation)
                                @php
                                    // Déterminer qui est l'autre participant
                                    $otherParticipant = $conversation->buyer_id == Auth::id() ? $conversation->seller : $conversation->buyer;
                                @endphp
                                <a href="{{ route('conversations.show', $conversation) }}" class="block p-4 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
                                    <div class="flex items-start space-x-4">
                                        <!-- Image de l'article -->
                                        <div class="flex-shrink-0">
                                            @if($conversation->item && $conversation->item->primaryImage)
                                                <img class="w-20 h-20 rounded-md object-cover" src="{{ asset('storage/' . $conversation->item->primaryImage->path) }}" alt="{{ $conversation->item->title }}">
                                            @else
                                                <div class="w-20 h-20 rounded-md bg-gray-200 flex items-center justify-center">
                                                    <span class="text-xs text-gray-500">Aucune image</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-grow">
                                            <!-- Titre de l'article et nom de l'interlocuteur -->
                                            <div class="flex justify-between items-center">
                                                <p class="font-semibold text-lg">{{ $conversation->item->title ?? 'Article supprimé' }}</p>
                                                <p class="text-sm text-gray-600">
                                                    Conversation avec <span class="font-medium">{{ $otherParticipant->name }}</span>
                                                </p>
                                            </div>

                                            <!-- Dernier message -->
                                            <div class="mt-2 text-sm text-gray-700">
                                                @if($conversation->latestMessage)
                                                    <p class="truncate">
                                                        <span class="font-semibold">{{ $conversation->latestMessage->user_id == Auth::id() ? 'Vous' : $conversation->latestMessage->user->name }}:</span>
                                                        {{ $conversation->latestMessage->content }}
                                                    </p>
                                                @else
                                                    <p class="text-gray-500 italic">Aucun message pour le moment.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
