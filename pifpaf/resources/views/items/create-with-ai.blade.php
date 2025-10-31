<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Vendre un article avec l\'IA') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" x-data="{ loading: false }">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('success')" />
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <!-- Custom Error Message -->
                    @if(session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form action="{{ route('items.analyze-image') }}" method="POST" enctype="multipart/form-data" @submit="loading = true">
                        @csrf

                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">{{ __('Choisissez une photo de votre article') }}</label>
                            <input id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="file" name="image" required autofocus />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <div x-show="loading" class="text-gray-500 italic mr-4">
                                {{ __('Analyse de l\'image en cours...') }}
                            </div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" :disabled="loading">
                                <span x-show="!loading">{{ __('Analyser l\'image') }}</span>
                                <span x-show="loading">{{ __('Veuillez patienter...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
