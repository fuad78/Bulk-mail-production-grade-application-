<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 leading-tight tracking-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex space-x-2">
                <!-- Date Range Picker Placeholder -->
                <button
                    class="flex items-center px-3 py-2 bg-white border border-gray-200 rounded-md text-sm text-gray-600 shadow-sm hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Last 7 Days
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Smart Action Required Section -->
        @if(count($todoItems) > 0)
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Action Required
                    </h3>
                    <span class="text-sm text-gray-500">{{ count($todoItems) }} items pending</span>
                </div>
                <div class="space-y-4">
                    @foreach($todoItems as $item)
                        <div class="flex items-center justify-between p-4 rounded-xl border {{ $item['type'] === 'error' ? 'bg-red-50 border-red-100' : ($item['type'] === 'warning' ? 'bg-yellow-50 border-yellow-100' : 'bg-blue-50 border-blue-100') }}">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @if($item['type'] === 'error')
                                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    @elseif($item['type'] === 'warning')
                                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">{{ $item['message'] }}</h4>
                                    <p class="text-xs text-gray-500">{{ $item['date']->diffForHumans() }}</p>
                                </div>
                            </div>
                            <a href="{{ $item['action_url'] }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white {{ $item['type'] === 'error' ? 'bg-red-600 hover:bg-red-700' : ($item['type'] === 'warning' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-blue-600 hover:bg-blue-700') }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ $item['action_text'] }}
                                <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Live Delivery Status -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6 relative overflow-hidden">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span class="relative flex h-3 w-3 mr-2">
                  @if($pendingEmails > 0)
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  @endif
                  <span class="relative inline-flex rounded-full h-3 w-3 {{ $pendingEmails > 0 ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                </span>
                Live Email Status
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-xs font-bold text-blue-400 uppercase">Successfully Sent</p>
                    <p class="text-2xl font-bold text-blue-700">{{ number_format($totalSent) }}</p>
                </div>
                <div class="bg-yellow-50 rounded-xl p-4">
                    <p class="text-xs font-bold text-yellow-500 uppercase">Pending (In Queue)</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ number_format($pendingEmails) }}</p>
                    @if($pendingEmails > 0)
                        <p class="text-xs text-yellow-600 mt-1 animate-pulse">Sending in progress...</p>
                    @endif
                </div>
                <div class="bg-red-50 rounded-xl p-4">
                    <p class="text-xs font-bold text-red-400 uppercase">Failed</p>
                    <p class="text-2xl font-bold text-red-700">{{ number_format($failedEmails) }}</p>
                </div>
            </div>

            @if($pendingEmails > 0)
                <div>
                    <div class="flex justify-between text-sm font-medium text-gray-600 mb-1">
                        <span>Current Job Progress</span>
                        <span>{{ $completionPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-out" style="width: {{ $completionPercentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2 text-right">Auto-refreshes every 10s</p>
                </div>
            @endif
        </div>

        <script>
            @if($pendingEmails > 0)
                setTimeout(function() {
                    window.location.reload();
                }, 10000);
            @endif
        </script>

        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Sent Emails -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between h-32">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Emails Sent</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($totalSent) }}</h3>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center text-sm">
                    <span class="text-green-500 font-medium flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                        12%
                    </span>
                    <span class="text-gray-400 ml-2">vs last week</span>
                </div>
            </div>

            <!-- Open Rate -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between h-32">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Open Rate</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $openRate }}%</h3>
                    </div>
                    <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                    <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $openRate }}%"></div>
                </div>
            </div>

            <!-- Click Rate (CTR) -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between h-32">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Click Rate</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $clickRate }}%</h3>
                    </div>
                    <div class="p-2 bg-green-50 rounded-lg text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                    <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $clickRate }}%"></div>
                </div>
            </div>

            <!-- Bounce Rate -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between h-32">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Bounce Rate</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $bounceRate }}%</h3>
                    </div>
                    <div class="p-2 bg-red-50 rounded-lg text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center text-sm">
                    <span class="text-gray-400">Total Bounces:
                        {{ \App\Models\Recipient::whereNotNull('bounced_at')->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Charts & Lists Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Chart Area -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Performance Over Time</h3>
                    <select
                        class="text-sm border-gray-200 rounded-md text-gray-500 focus:ring-blue-500 focus:border-blue-500">
                        <option>Emails Sent</option>
                        <option>Opens</option>
                    </select>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <!-- Recent Campaigns List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-0 overflow-hidden flex flex-col">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">Recent Campaigns</h3>
                    <a href="{{ route('campaigns.index') }}"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium">View All</a>
                </div>
                <div class="flex-1 overflow-y-auto overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentCampaigns as $campaign)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full text-xs font-bold text-blue-600 bg-blue-100">
                                                {{ substr($campaign->subject, 0, 2) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 truncate w-40">
                                                    {{ $campaign->subject }}</div>
                                                <div class="text-xs text-gray-500">
                                                    Sent: {{ $campaign->sent_count }} | 
                                                    Failed: <span class="{{ $campaign->failed_count > 0 ? 'text-red-600 font-bold' : '' }}">{{ $campaign->failed_count }}</span> |
                                                    Open: {{ number_format($campaign->sent_count > 0 ? ($campaign->open_count / $campaign->sent_count * 100) : 0, 0) }}%
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ ucfirst($campaign->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">No campaigns yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: 'Emails Sent',
                        data: @json($chartData['sent']),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Opens',
                        data: @json($chartData['opened']),
                        borderColor: '#a855f7',
                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>