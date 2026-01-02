<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('POST')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 border-b border-gray-200">
                        <h3 class="text-lg font-bold mb-4">General Settings</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Application Name</label>
                                <input type="text" name="app_name" value="{{ $settings['app_name'] }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="text-xs text-gray-500 mt-1">This name will appear on the dashboard and emails.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 text-gray-900 border-b border-gray-200">
                        <h3 class="text-lg font-bold mb-4">Mail Configuration</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Mailer</label>
                                <select name="mail_mailer"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="smtp" {{ $settings['mail_mailer'] == 'smtp' ? 'selected' : '' }}>SMTP
                                    </option>
                                    <option value="ses" {{ $settings['mail_mailer'] == 'ses' ? 'selected' : '' }}>AWS SES
                                    </option>
                                    <option value="log" {{ $settings['mail_mailer'] == 'log' ? 'selected' : '' }}>Log
                                        (Testing)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">From Address</label>
                                <input type="email" name="mail_from_address"
                                    value="{{ $settings['mail_from_address'] }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="p-6 text-gray-900 border-b border-gray-200">
                        <h4 class="font-semibold mb-2 text-gray-600">SMTP Settings (Required if Mailer is SMTP)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Host</label>
                                <input type="text" name="mail_host" value="{{ $settings['mail_host'] }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Port</label>
                                <input type="text" name="mail_port" value="{{ $settings['mail_port'] }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Username</label>
                                <input type="text" name="mail_username" value="{{ $settings['mail_username'] }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Password</label>
                                <input type="password" name="mail_password" placeholder="Leave blank to keep current"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Encryption</label>
                                <select name="mail_encryption"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="tls" {{ ($settings['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="null" {{ empty($settings['mail_encryption']) ? 'selected' : '' }}>
                                        None</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 text-gray-900">
                        <h4 class="font-semibold mb-2 text-gray-600">AWS SES Settings (Required if Mailer is AWS SES)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Access Key ID</label>
                                <input type="text" name="aws_access_key_id" value="{{ $settings['aws_access_key_id'] }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Secret Access Key</label>
                                <input type="password" name="aws_secret_access_key"
                                    placeholder="Leave blank to keep current"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Region</label>
                                <input type="text" name="aws_default_region"
                                    value="{{ $settings['aws_default_region'] }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>