<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $campaign->subject }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Status Message -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Campaign Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-bold mb-2">Details</h3>
                            <p><strong>Status:</strong>
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $campaign->status === 'approved' ? 'green' : ($campaign->status === 'pending_approval' ? 'yellow' : 'gray') }}-100 text-gray-800">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </p>
                            <p class="mt-2"><strong>Scheduled:</strong>
                                {{ $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d H:i') : 'Not scheduled' }}
                            </p>
                            <p class="mt-2"><strong>Department:</strong> {{ $campaign->department->name ?? 'N/A' }}</p>
                            <p class="mt-2"><strong>Recipients:</strong> {{ $campaign->recipients()->count() }}</p>
                            <p class="mt-2"><strong>Sender:</strong> 
                                @if($campaign->sender)
                                    {{ $campaign->sender->name }} ({{ $campaign->sender->email }})
                                @else
                                    <span class="text-gray-500 italic">Default System Email</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold mb-2">Content</h3>
                            <div class="bg-gray-50 p-4 rounded border">
                                {!! nl2br(e($campaign->body)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Actions</h3>

                    <div class="flex flex-wrap gap-4">
                        <!-- Upload Recipients (Draft Only) -->
                        @if($campaign->status === 'draft')
                            <div class="w-full max-w-xs space-y-4">
                                <!-- Option 1: CSV Upload -->
                                <form action="{{ route('campaigns.upload', $campaign) }}" method="POST"
                                    enctype="multipart/form-data" class="space-y-2 p-3 border rounded bg-gray-50">
                                    @csrf
                                    <label class="block text-sm font-medium text-gray-700">Option 1: Upload CSV</label>
                                    <p class="text-xs text-gray-500 mb-2">Format: <code>email,name</code></p>
                                    <input type="file" name="file" accept=".csv,.txt"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        Upload File
                                    </button>
                                </form>

                                <!-- Option 2: Address Book -->
                                <form action="{{ route('campaigns.import_list', $campaign) }}" method="POST"
                                    class="space-y-2 p-3 border rounded bg-gray-50">
                                    @csrf
                                    <label class="block text-sm font-medium text-gray-700">Option 2: Address Book</label>
                                    <select name="contact_list_id"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select a saved list...</option>
                                        @foreach($availableLists as $list)
                                            <option value="{{ $list->id }}">{{ $list->name }}
                                                ({{ $list->contacts_count ?? '0' }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none transition ease-in-out duration-150">
                                        Load List
                                    </button>
                                </form>
                            </div>

                            <form action="{{ route('campaigns.submit', $campaign) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none transition ease-in-out duration-150">
                                    Submit for Approval
                                </button>
                            </form>
                        @endif

                        <!-- Approval Actions (Admin Only & Pending) -->
                        @if($campaign->status === 'pending_approval' && auth()->user()->isAdmin())
                            <form action="{{ route('campaigns.approve', $campaign) }}" method="POST" class="space-y-4 border p-4 rounded bg-green-50">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Sender (Source Email)</label>
                                    <select name="sender_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                                        <option value="">Default System Email</option>
                                        @foreach($senders as $sender)
                                            <option value="{{ $sender->id }}" {{ $campaign->sender_id == $sender->id ? 'selected' : '' }}>
                                                {{ $sender->name }} ({{ $sender->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none transition ease-in-out duration-150">
                                    Approve & Send
                                </button>
                            </form>

                            <form action="{{ route('campaigns.reject', $campaign) }}" method="POST"
                                class="flex items-center gap-2">
                                @csrf
                                <input type="text" name="reason" placeholder="Rejection reason" required
                                    class="border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 text-sm">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none transition ease-in-out duration-150">
                                    Reject
                                </button>
                            </form>
                        @endif

                        <!-- Retry Failed (Admin Only) -->
                        @if(($campaign->status === 'sending' || $campaign->status === 'completed') && $campaign->recipients()->where('status', 'failed')->exists() && auth()->user()->isAdmin())
                            <form action="{{ route('campaigns.retry', $campaign) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none transition ease-in-out duration-150">
                                    Retry Failed Emails ({{ $campaign->recipients()->where('status', 'failed')->count() }})
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recipient Debug List (For Debugging) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Recipient Status</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Error Message</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($campaign->recipients()->latest()->take(50)->get() as $recipient)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $recipient->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($recipient->status === 'sent')
                                                <span class="text-green-600 font-bold">Sent</span>
                                            @elseif($recipient->status === 'failed')
                                                <span class="text-red-600 font-bold">Failed</span>
                                            @else
                                                <span class="text-yellow-600">{{ ucfirst($recipient->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-red-500 font-mono text-xs">
                                            {{ $recipient->error_message ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $recipient->updated_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>