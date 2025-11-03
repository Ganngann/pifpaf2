<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Style Guide') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold mb-4">Style Guide</h1>

                    <!-- Typography -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Typography</h2>
                        <div class="space-y-4">
                            <h1>h1: The quick brown fox jumps over the lazy dog</h1>
                            <h2>h2: The quick brown fox jumps over the lazy dog</h2>
                            <h3>h3: The quick brown fox jumps over the lazy dog</h3>
                            <h4>h4: The quick brown fox jumps over the lazy dog</h4>
                            <h5>h5: The quick brown fox jumps over the lazy dog</h5>
                            <h6>h6: The quick brown fox jumps over the lazy dog</h6>
                            <p>p: The quick brown fox jumps over the lazy dog. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.</p>
                            <blockquote class="border-l-4 border-gray-400 pl-4 italic">
                                Blockquote: The quick brown fox jumps over the lazy dog.
                            </blockquote>
                            <ul class="list-disc list-inside">
                                <li>Unordered List Item 1</li>
                                <li>Unordered List Item 2</li>
                                <li>Unordered List Item 3</li>
                            </ul>
                            <ol class="list-decimal list-inside">
                                <li>Ordered List Item 1</li>
                                <li>Ordered List Item 2</li>
                                <li>Ordered List Item 3</li>
                            </ol>
                        </div>
                    </section>

                    <!-- Buttons -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Buttons</h2>
                        <div class="flex flex-wrap gap-4 items-center">
                            <x-primary-button>{{ __('Primary Button') }}</x-primary-button>
                            <button class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">{{ __('Secondary') }}</button>
                            <button class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">{{ __('Success') }}</button>
                            <button class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">{{ __('Danger') }}</button>
                            <button class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">{{ __('Warning') }}</button>
                            <button class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">{{ __('Info') }}</button>
                            <x-primary-button disabled>{{ __('Disabled') }}</x-primary-button>
                        </div>
                    </section>

                    <!-- Form Elements -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Form Elements</h2>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="text_input" :value="__('Text Input')" />
                                <x-text-input id="text_input" class="block mt-1 w-full" type="text" name="text_input" :value="old('text_input')" required autofocus />
                            </div>
                            <div>
                                <x-input-label for="textarea" :value="__('Textarea')" />
                                <textarea id="textarea" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            </div>
                            <div>
                                <x-input-label for="select" :value="__('Select')" />
                                <select id="select" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option>Option 1</option>
                                    <option>Option 2</option>
                                    <option>Option 3</option>
                                </select>
                            </div>
                            <div class="flex items-center">
                                <input id="checkbox" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="checkbox" class="ml-2 block text-sm text-gray-900">{{ __('Checkbox') }}</label>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Radio Buttons</label>
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-center">
                                        <input id="radio1" name="radio" type="radio" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        <label for="radio1" class="ml-2 block text-sm text-gray-900">Radio Option 1</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="radio2" name="radio" type="radio" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        <label for="radio2" class="ml-2 block text-sm text-gray-900">Radio Option 2</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Alerts -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Alerts</h2>
                        <div class="space-y-4">
                            <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700" role="alert">
                                <p class="font-bold">Success</p>
                                <p>This is a success alert.</p>
                            </div>
                            <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700" role="alert">
                                <p class="font-bold">Danger</p>
                                <p>This is a danger alert.</p>
                            </div>
                            <div class="p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700" role="alert">
                                <p class="font-bold">Warning</p>
                                <p>This is a warning alert.</p>
                            </div>
                            <div class="p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700" role="alert">
                                <p class="font-bold">Info</p>
                                <p>This is an info alert.</p>
                            </div>
                        </div>
                    </section>

                    <!-- Badges -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Badges</h2>
                        <div class="flex flex-wrap gap-4 items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Gray</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Red</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Yellow</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Green</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Blue</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Indigo</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Purple</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">Pink</span>
                        </div>
                    </section>

                    <!-- Cards -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Cards</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                                <img class="w-full h-48 object-cover" src="https://placehold.co/400x250" alt="Placeholder">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold mb-2">Card Title</h3>
                                    <p class="text-gray-700">This is a simple card component. It can be used to display information in a structured way.</p>
                                    <div class="mt-4">
                                        <x-primary-button>Action</x-primary-button>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold mb-2">Card without Image</h3>
                                    <p class="text-gray-700">This is a card without an image. Useful for text-based content.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Modals -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Modals</h2>
                        <div x-data="{ open: false }">
                            <x-primary-button @click="open = true">Open Modal</x-primary-button>
                            <!-- Modal -->
                            <div x-show="open"
                                 class="fixed z-10 inset-0 overflow-y-auto"
                                 aria-labelledby="modal-title"
                                 role="dialog"
                                 aria-modal="true"
                                 x-cloak>
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <!-- Background overlay -->
                                    <div x-show="open"
                                         @click="open = false"
                                         x-transition:enter="ease-out duration-300"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="ease-in duration-200"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                         aria-hidden="true"></div>

                                    <!-- This element is to trick the browser into centering the modal contents. -->
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                                    <!-- Heroicon name: outline/information-circle -->
                                                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                        Modal Title
                                                    </h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500">
                                                            This is the modal content. You can put any information you want here.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <button @click="open = false" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                Confirm
                                            </button>
                                            <button @click="open = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Navigation -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Navigation</h2>

                        <!-- Breadcrumbs -->
                        <nav class="mb-4" aria-label="Breadcrumb">
                            <ol class="list-none p-0 inline-flex">
                                <li class="flex items-center">
                                    <a href="#" class="text-gray-500 hover:text-gray-700">Home</a>
                                    <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                                </li>
                                <li class="flex items-center">
                                    <a href="#" class="text-gray-500 hover:text-gray-700">Category</a>
                                    <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>
                                </li>
                                <li>
                                    <a href="#" class="text-gray-600">Current Page</a>
                                </li>
                            </ol>
                        </nav>

                        <!-- Pagination -->
                        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
                            <div class="flex justify-between flex-1 sm:hidden">
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                                    &laquo; Previous
                                </span>

                                <a href="#" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    Next &raquo;
                                </a>
                            </div>

                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 leading-5">
                                        Showing
                                        <span class="font-medium">1</span>
                                        to
                                        <span class="font-medium">10</span>
                                        of
                                        <span class="font-medium">100</span>
                                        results
                                    </p>
                                </div>

                                <div>
                                    <span class="relative z-0 inline-flex shadow-sm rounded-md">
                                        <span aria-disabled="true" aria-label="Previous">
                                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </span>

                                        <span aria-current="page">
                                            <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5">1</span>
                                        </span>
                                        <a href="#" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page 2">
                                            2
                                        </a>
                                        <a href="#" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page 3">
                                            3
                                        </a>

                                        <a href="#" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Next">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </nav>

                    </section>

                    <!-- Tables -->
                    <section class="mb-8">
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Tables</h2>
                        <div class="overflow-x-auto">
                            <!-- Desktop Table -->
                            <table class="min-w-full bg-white hidden md:table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Edit</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Jane Cooper</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Regional Paradigm Technician</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">jane.cooper@example.com</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Admin</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        </td>
                                    </tr>
                                    <!-- More people... -->
                                </tbody>
                            </table>

                            <!-- Mobile Cards -->
                            <div class="grid grid-cols-1 gap-4 md:hidden">
                                <div class="bg-white p-4 rounded-lg shadow">
                                    <div class="flex justify-between items-center mb-2">
                                        <div class="text-sm font-medium text-gray-900">Jane Cooper</div>
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <p><span class="font-medium text-gray-700">Title:</span> Regional Paradigm Technician</p>
                                        <p><span class="font-medium text-gray-700">Email:</span> jane.cooper@example.com</p>
                                        <p><span class="font-medium text-gray-700">Role:</span> Admin</p>
                                    </div>
                                </div>
                                <!-- More cards... -->
                            </div>
                        </div>
                    </section>

                    <!-- Spinners -->
                    <section>
                        <h2 class="text-xl font-semibold mb-4 border-b pb-2">Spinners</h2>
                        <div class="flex items-center space-x-4">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                            <div class="animate-ping h-6 w-6 rounded-full bg-green-400"></div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
