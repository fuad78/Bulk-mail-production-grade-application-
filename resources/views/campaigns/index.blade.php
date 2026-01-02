<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <!-- Top Toolbar -->
        <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <div class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                            <span class="mr-2">Campaigns</span>
                            <span class="mx-2">/</span>
                            <span class="text-gray-900 font-bold">Email Marketing</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('campaigns.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none ring-2 ring-offset-2 ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            New Campaign
                        </a>
                    </div>
                </div>
            </div>
            <!-- Tabs -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex space-x-8 -mb-px">
                    <a href="#"
                        class="border-indigo-500 text-indigo-600 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        Tasks
                    </a>
                    <a href="#"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        Board
                    </a>
                    <a href="{{ route('calendar.index') }}"
                        class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                        Calendar
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

            @php
                // Helper to group statuses
                // PLANNED: draft
                // SCHEDULED: pending_approval, approved, scheduled
                // ACTIVE: sending, completed, rejected (rejected in active to see result)

                // We rely on the paginated $campaigns here. 
                // For a real app, we might want separate queries, but this works for the UI demo.

                $grouped = $campaigns->groupBy(function ($item) {
                    if (in_array($item->status, ['draft']))
                        return 'PLANNED';
                    if (in_array($item->status, ['pending_approval', 'approved', 'scheduled']))
                        return 'SCHEDULED';
                    return 'ACTIVE';
                });

                // Ensure groups exist for visual consistency if empty
                $groups = ['PLANNED' => 'bg-blue-400', 'SCHEDULED' => 'bg-green-500', 'ACTIVE' => 'bg-green-600'];
            @endphp

            @foreach($groups as $groupName => $badgeColor)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Group Header -->
                    <div class="bg-gray-50 border-b border-gray-100 px-6 py-3 grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-6 flex items-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold leading-4 text-white uppercase tracking-wider {{ $badgeColor }}">
                                {{ $groupName }}
                            </span>
                        </div>
                        <div class="col-span-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Deadline</div>
                        <div class="col-span-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Action Required
                        </div>
                        <div class="col-span-2 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Status</div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @if(isset($grouped[$groupName]))
                            @foreach($grouped[$groupName] as $campaign)
                                <div
                                    class="px-6 py-4 grid grid-cols-12 gap-4 items-center hover:bg-gray-50 transition-colors group">
                                    <!-- Title Section -->
                                    <div class="col-span-6">
                                        <div class="flex items-center">
                                            <a href="{{ route('campaigns.show', $campaign) }}"
                                                class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                                                {{ $campaign->subject }}
                                            </a>
                                        </div>
                                        <div class="mt-1 flex items-center text-xs text-gray-500">
                                            <span class="truncate">{{ $campaign->department->name ?? 'General' }}</span>
                                            <span class="mx-1">&bull;</span>
                                            <span>Created by {{ $campaign->user->name }}</span>
                                        </div>
                                    </div>

                                    <!-- Deadline -->
                                    <div class="col-span-2 text-sm text-gray-600">
                                        {{ $campaign->scheduled_at ? $campaign->scheduled_at->format('M j, Y') : '-' }}
                                    </div>

                                    <!-- Action Required -->
                                    <div class="col-span-2 text-sm text-gray-500">
                                        @if($campaign->status === 'draft')
                                            <a href="{{ route('campaigns.show', $campaign) }}"
                                                class="text-indigo-600 hover:text-indigo-800 font-medium">Configure</a>
                                        @elseif($campaign->status === 'pending_approval')
                                            @if(auth()->user()->isAdmin())
                                                <span class="text-orange-600 font-medium">Review Now</span>
                                            @else
                                                <span class="text-gray-400">Waiting for Admin</span>
                                            @endif
                                        @elseif($campaign->status === 'sending')
                                            <span class="text-green-600 flex items-center">
                                                <span class="relative flex h-2 w-2 mr-2">
                                                    <span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                                </span>
                                                Sending...
                                            </span>
                                        @else
                                            No action
                                        @endif
                                    </div>

                                    <!-- Status -->
                                    <div class="col-span-2 text-right">
                                        <span class="text-sm font-bold text-gray-700 uppercase">
                                            {{ $campaign->status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="px-6 py-8 text-center text-sm text-gray-400 italic">
                                No campaigns in this stage.
                            </div>
                        @endif

                        <!-- Quick Add Row (Visual Only for now) -->
                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                            <a href="{{ route('campaigns.create') }}"
                                class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New task
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="mt-8">
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
</x-app-layout>