<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                            <input id="name"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mt-4">
                            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                            <input id="email"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                type="email" name="email" value="{{ old('email', $user->email) }}" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Designation -->
                        <div class="mt-4">
                            <label for="designation" class="block font-medium text-sm text-gray-700">Designation</label>
                            <input id="designation"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                type="text" name="designation" value="{{ old('designation', $user->designation) }}" />
                            <x-input-error :messages="$errors->get('designation')" class="mt-2" />
                        </div>

                        <!-- Phone -->
                        <div class="mt-4">
                            <label for="phone" class="block font-medium text-sm text-gray-700">Phone</label>
                            <input id="phone"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                type="text" name="phone" value="{{ old('phone', $user->phone) }}" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <label for="role" class="block font-medium text-sm text-gray-700">Role</label>
                            <select id="role" name="role"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="viewer" {{ $user->role === 'viewer' ? 'selected' : '' }}>Viewer (Read Only)
                                </option>
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User (Standard)
                                </option>
                                <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager
                                    (Approve)</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin (Full Access)
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Daily Send Limit -->
                        <div class="mt-4">
                            <label for="daily_send_limit" class="block font-medium text-sm text-gray-700">Daily Send
                                Limit</label>
                            <input id="daily_send_limit"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                type="number" name="daily_send_limit"
                                value="{{ old('daily_send_limit', $user->daily_send_limit) }}" min="0" />
                            <p class="text-xs text-gray-500 mt-1">Maximum emails this user can send per day. Set to 0
                                for unlimited.</p>
                            <x-input-error :messages="$errors->get('daily_send_limit')" class="mt-2" />
                        </div>

                        <!-- Department -->
                        <div class="mt-4">
                            <label for="department_id" class="block font-medium text-sm text-gray-700">
                                Department
                                <a href="{{ route('departments.create') }}"
                                    class="text-indigo-600 hover:text-indigo-900 ml-2 text-xs">(+ Add New)</a>
                            </label>
                            <select id="department_id" name="department_id"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $user->department_id === $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                        </div>

                        <!-- Password (Optional) -->
                        <div class="mt-4">
                            <label for="password" class="block font-medium text-sm text-gray-700">Password (Leave blank
                                to keep current)</label>
                            <input id="password"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                type="password" name="password" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirm
                                Password</label>
                            <input id="password_confirmation"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                type="password" name="password_confirmation" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('users.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>