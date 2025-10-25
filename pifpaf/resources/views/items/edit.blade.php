<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier l\'annonce') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div>
                            <x-label for="title" :value="__('Titre')" />
                            <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $item->title)" required autofocus />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $item->description) }}</textarea>
                        </div>

                        <!-- Price -->
                        <div class="mt-4">
                            <x-label for="price" :value="__('Prix')" />
                            <x-input id="price" class="block mt-1 w-full" type="text" name="price" :value="old('price', $item->price)" required />
                        </div>

                        <!-- Image -->
                        <div class="mt-4">
                            <x-label for="image" :value="__('Image')" />
                            <x-input id="image" class="block mt-1 w-full" type="file" name="image" />
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="h-20 w-20 object-cover">
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Mettre à jour') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
