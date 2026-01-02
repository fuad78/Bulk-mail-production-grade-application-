<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <!-- Top Toolbar -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <div class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                            <span class="mr-2">Contacts</span>
                            <span class="mx-2">/</span>
                            <span class="text-gray-900 font-bold">Address Books</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('lists.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none ring-2 ring-offset-2 ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            New List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($lists as $list)
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 truncate" title="{{ $list->name }}">
                                        {{ $list->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $list->department->name ?? 'Global' }}</p>
                                </div>
                                <div
                                    class="h-10 w-10 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                </div>
                            </div>

                            <div class="mt-4">
                                <p class="text-sm text-gray-600 line-clamp-2 min-h-[2.5rem]">
                                    {{ $list->description ?? 'No description provided.' }}
                                </p>
                            </div>

                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $list->contacts_count }} <span class="text-gray-500 font-normal">contacts</span>
                                </div>
                                <div class="text-xs text-gray-400">
                                    Updated {{ $list->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex items-center justify-between">
                            <a href="{{ route('lists.show', $list) }}"
                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Contacts</a>

                            @if(auth()->user()->isAdmin() || auth()->user()->id === $list->user_id)
                                <form action="{{ route('lists.destroy', $list) }}" method="POST"
                                    onsubmit="return confirm('Delete this list? All contacts in it will be removed from the Address Book (but not from sent campaigns).');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No lists created</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new contact list.</p>
                        <div class="mt-6">
                            <a href="{{ route('lists.create') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none ring-2 ring-offset-2 ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                New List
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="mt-8">
                {{ $lists->links() }}
            </div>
        </div>
    </div>
</x-app-layout>