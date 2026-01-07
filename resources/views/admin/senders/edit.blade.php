<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Sender') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('senders.update', $sender->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Identity</h3>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Sender Name
                                        (Internal)</label>
                                    <input type="text" name="name" value="{{ $sender->name }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">From Email Address</label>
                                    <input type="email" name="email" value="{{ $sender->email }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Type</label>
                                    <select name="type" id="type_selector" onchange="toggleConfig()"
                                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <option value="smtp" {{ $sender->type == 'smtp' ? 'selected' : '' }}>SMTP
                                            (Mailtrap, Gmail, etc.)</option>
                                        <option value="ses" {{ $sender->type == 'ses' ? 'selected' : '' }}>AWS SES (API)
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="is_active" value="1" {{ $sender->is_active ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-blue-600">
                                        <span class="ml-2 text-gray-700">Active</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Configuration -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Configuration</h3>

                                <!-- SMTP Config -->
                                <div id="smtp_config" class="space-y-4 {{ $sender->type != 'smtp' ? 'hidden' : '' }}">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-1">Host</label>
                                        <input type="text" name="configuration[host]"
                                            value="{{ $sender->configuration['host'] ?? '' }}"
                                            class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                    </div>
                                    <div class="flex gap-4">
                                        <div class="w-1/2">
                                            <label class="block text-gray-700 text-sm font-bold mb-1">Port</label>
                                            <input type="text" name="configuration[port]"
                                                value="{{ $sender->configuration['port'] ?? '' }}"
                                                class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                        </div>
                                        <div class="w-1/2">
                                            <label class="block text-gray-700 text-sm font-bold mb-1">Encryption</label>
                                            <input type="text" name="configuration[encryption]"
                                                value="{{ $sender->configuration['encryption'] ?? '' }}"
                                                class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-1">Username</label>
                                        <input type="text" name="configuration[username]"
                                            value="{{ $sender->configuration['username'] ?? '' }}"
                                            class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-1">Password</label>
                                        <input type="password" name="configuration[password]"
                                            value="{{ $sender->configuration['password'] ?? '' }}"
                                            class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                    </div>
                                </div>

                                <!-- SES Config -->
                                <div id="ses_config" class="space-y-4 {{ $sender->type != 'ses' ? 'hidden' : '' }}">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-1">AWS Access Key
                                            ID</label>
                                        <input type="text" name="configuration[key]"
                                            value="{{ $sender->configuration['key'] ?? '' }}"
                                            class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-1">AWS Secret Access
                                            Key</label>
                                        <input type="password" name="configuration[secret]"
                                            value="{{ $sender->configuration['secret'] ?? '' }}"
                                            class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-1">Region</label>
                                        <input type="text" name="configuration[region]"
                                            value="{{ $sender->configuration['region'] ?? '' }}"
                                            class="shadow border rounded w-full py-2 px-3 text-gray-700">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Sender Profile
                            </button>
                        </div>
                    </form>

                    <script>
                        function toggleConfig() {
                            const type = document.getElementById('type_selector').value;
                            const smtp = document.getElementById('smtp_config');
                            const ses = document.getElementById('ses_config');

                            if (type === 'smtp') {
                                smtp.classList.remove('hidden');
                                ses.classList.add('hidden');
                            } else {
                                smtp.classList.add('hidden');
                                ses.classList.remove('hidden');
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>