<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Recipient;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Sent
        $totalSent = Recipient::where('status', 'sent')->count();

        // 2. Open Rate
        $totalOpens = Recipient::whereNotNull('opened_at')->count();
        $openRate = $totalSent > 0 ? round(($totalOpens / $totalSent) * 100, 1) : 0;

        // 3. Click Rate
        $totalClicks = Recipient::whereNotNull('clicked_at')->count();
        $clickRate = $totalSent > 0 ? round(($totalClicks / $totalSent) * 100, 1) : 0;

        // 4. Bounces
        $totalBounces = Recipient::whereNotNull('bounced_at')->count();
        $bounceRate = $totalSent > 0 ? round(($totalBounces / $totalSent) * 100, 1) : 0;

        // 5. Recent Campaigns for Table
        $recentCampaigns = Campaign::with('user')
            ->withCount([
                'recipients as sent_count' => function ($query) {
                    $query->where('status', 'sent');
                }
            ])
            ->withCount([
                'recipients as open_count' => function ($query) {
                    $query->whereNotNull('opened_at');
                }
            ])
            ->latest()
            ->take(5)
            ->get();

        // 6. Chart Data (Last 7 Days)
        $chartData = $this->getChartData();

        return view('dashboard', compact(
            'totalSent',
            'openRate',
            'clickRate',
            'bounceRate',
            'recentCampaigns',
            'chartData'
        ));
    }

    private function getChartData()
    {
        // Dummy data for now, or fetch from DB by date
        $labels = [];
        $sentData = [];
        $openData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('M d');

            // In a real app with 'sent_at' on recipients, we would query this:
            // Recipient::whereDate('sent_at', $date)->count();
            // creating dummy randomization for visual effect if 0
            $sentData[] = Recipient::whereDate('updated_at', $date)->where('status', 'sent')->count();
            $openData[] = Recipient::whereDate('opened_at', $date)->count();
        }

        return [
            'labels' => $labels,
            'sent' => $sentData,
            'opened' => $openData
        ];
    }
}
