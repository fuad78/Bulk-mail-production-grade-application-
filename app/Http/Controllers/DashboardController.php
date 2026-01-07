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

        // 5. LIVE Queue Stats
        $pendingEmails = Recipient::where('status', 'pending')->count();
        $failedEmails = Recipient::where('status', 'failed')->count();
        $totalProcessed = $totalSent + $failedEmails;
        // Calculate a "Global Completion Rate" if there are pending items
        $totalInPipeline = $totalProcessed + $pendingEmails;
        $completionPercentage = $totalInPipeline > 0 ? round(($totalProcessed / $totalInPipeline) * 100, 1) : 100;

        // 6. Recent Campaigns for Table
        $recentCampaigns = Campaign::with('user')
            ->withCount([
                'recipients as sent_count' => function ($query) {
                    $query->where('status', 'sent');
                }
            ])
            ->withCount([
                'recipients as failed_count' => function ($query) {
                    $query->where('status', 'failed');
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

        // 6. Smart To-Do List Items
        $todoItems = [];

        // Drafts
        $drafts = Campaign::where('status', Campaign::STATUS_DRAFT)
            ->where('user_id', auth()->id()) // Only own drafts
            ->latest()
            ->get();

        foreach ($drafts as $draft) {
            $todoItems[] = [
                'type' => 'info',
                'message' => "Finish setting up campaign: {$draft->subject}",
                'action_url' => route('campaigns.edit', $draft),
                'action_text' => 'Continue Editing',
                'date' => $draft->updated_at
            ];
        }

        // Rejected Campaigns
        $rejected = Campaign::where('status', Campaign::STATUS_REJECTED)
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        foreach ($rejected as $campaign) {
            $todoItems[] = [
                'type' => 'error',
                'message' => "Campaign rejected: {$campaign->subject}",
                'action_url' => route('campaigns.edit', $campaign), // Assuming edit to fix
                'action_text' => 'Review & Fix',
                'date' => $campaign->updated_at
            ];
        }

        // Pending Approval (if user is admin or approver - simplifying for now to show status)
        // Or if user is normal user, show "Waiting for approval"
        $pending = Campaign::where('status', Campaign::STATUS_PENDING_APPROVAL)
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        foreach ($pending as $campaign) {
            $todoItems[] = [
                'type' => 'warning',
                'message' => "Campaign pending approval: {$campaign->subject}",
                'action_url' => route('campaigns.show', $campaign),
                'action_text' => 'View Status',
                'date' => $campaign->updated_at
            ];
        }

        // Sort by date desc
        usort($todoItems, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        // 7. Chart Data (Last 7 Days)
        $chartData = $this->getChartData();

        return view('dashboard', compact(
            'totalSent',
            'openRate',
            'clickRate',
            'bounceRate',
            'recentCampaigns',
            'chartData',
            'todoItems',
            'pendingEmails',
            'failedEmails',
            'completionPercentage'
        ));
    }

    public function todo()
    {
        // Reuse logic or just fetch everything cleanly
        $todoItems = [];

        // Drafts
        $drafts = Campaign::where('status', Campaign::STATUS_DRAFT)
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        foreach ($drafts as $draft) {
            $todoItems[] = [
                'type' => 'info',
                'message' => "Finish setting up campaign: {$draft->subject}",
                'action_url' => route('campaigns.edit', $draft),
                'action_text' => 'Continue Editing',
                'date' => $draft->updated_at
            ];
        }

        // Rejected
        $rejected = Campaign::where('status', Campaign::STATUS_REJECTED)
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        foreach ($rejected as $campaign) {
            $todoItems[] = [
                'type' => 'error',
                'message' => "Campaign rejected: {$campaign->subject}",
                'action_url' => route('campaigns.edit', $campaign),
                'action_text' => 'Review & Fix',
                'date' => $campaign->updated_at
            ];
        }

        // Pending
        $pending = Campaign::where('status', Campaign::STATUS_PENDING_APPROVAL)
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        foreach ($pending as $campaign) {
            $todoItems[] = [
                'type' => 'warning',
                'message' => "Campaign pending approval: {$campaign->subject}",
                'action_url' => route('campaigns.show', $campaign),
                'action_text' => 'View Status',
                'date' => $campaign->updated_at
            ];
        }

        usort($todoItems, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('todo.index', compact('todoItems'));
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
