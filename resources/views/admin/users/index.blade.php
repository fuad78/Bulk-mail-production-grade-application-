<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <!-- Top Toolbar -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                         <div class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                             <span class="mr-2">System</span>
                             <span class="mx-2">/</span>
                             <span class="text-gray-900 font-bold">User Management</span>
                         </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none ring-2 ring-offset-2 ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Add New User
                        </a>
                    </div>
                </div>
            </div>
            <!-- Tabs (Visual for "Role" Filtering idea) -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex space-x-8 -mb-px">
                     <a href="#" class="border-indigo-500 text-indigo-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        All Users
                     </a>
                     <a href="{{ route('admin.access.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        Access Control
                     </a>
                     <a href="{{ route('admin.audit-logs.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        Audit Logs
                     </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            
            @php
                // Helper to group by Role
                // We rely on the paginated $users here. 
                // Grouping paginated results is slightly weird visually (users split across pages), 
                // but acceptable for this UI logic demonstrator.
                
                $grouped = $users->groupBy(function($item) {
                   return ucfirst($item->role);
                });
                
                // Define order and colors
                $groups = [
                    'Admin' => 'bg-purple-600', 
                    'Manager' => 'bg-blue-500', 
                    'User' => 'bg-gray-500'
                ];
            @endphp

            @foreach($groups as $groupName => $badgeColor)
                @if(isset($grouped[$groupName]) || $groupName == 'Admin') {{-- Show Admin group always even if empty on this page --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <div class="min-w-[800px]">
                                <!-- Group Header -->
                                <div class="bg-gray-50 border-b border-gray-100 px-6 py-3 grid grid-cols-12 gap-4 items-center">
                                    <div class="col-span-4 flex items-center">
                                         <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold leading-4 text-white uppercase tracking-wider {{ $badgeColor }}">
                                            {{ $groupName }}s
                                         </span>
                                    </div>
                                    <div class="col-span-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Contact Info</div>
                                    <div class="col-span-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Department</div>
                                    <div class="col-span-2 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</div>
                                </div>

                                <div class="divide-y divide-gray-100">
                                    @if(isset($grouped[$groupName]))
                                        @foreach($grouped[$groupName] as $user)
                                            <div class="px-6 py-4 grid grid-cols-12 gap-4 items-center hover:bg-gray-50 transition-colors group">
                                                <!-- Name Section -->
                                                <div class="col-span-4">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 mr-3 overflow-hidden">
                                                            @if($user->profile_photo_path)
                                                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" class="h-full w-full object-cover">
                                                            @else
                                                                {{ substr($user->name, 0, 2) }}
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                                                                {{ $user->name }}
                                                            </div>
                                                            <div class="text-xs text-gray-500">
                                                                {{ $user->designation ?? 'No designation' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Contact Info -->
                                                <div class="col-span-3 text-sm text-gray-600 flex flex-col justify-center">
                                                    <div>{{ $user->email }}</div>
                                                    <div class="text-xs text-gray-400">{{ $user->phone ?? '' }}</div>
                                                </div>

                                                <!-- Department -->
                                                <div class="col-span-3 text-sm text-gray-600">
                                                    @if($user->department)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $user->department->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </div>

                                                <!-- Actions -->
                                                <div class="col-span-2 text-right flex items-center justify-end space-x-3">
                                                    <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                                                    @if(auth()->id() !== $user->id)
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="px-6 py-4 text-center text-sm text-gray-400 italic">
                                            No users found in this role group on this page.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            <!-- Pagination -->
            <div class="mt-8">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>