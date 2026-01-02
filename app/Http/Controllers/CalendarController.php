<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Simple full calendar view
        $events = [];
        $campaigns = Campaign::whereNotNull('scheduled_at')->get();

        foreach ($campaigns as $c) {
            $color = '#3b82f6'; // Blue for default
            if ($c->status == 'completed')
                $color = '#10b981';
            if ($c->status == 'sending')
                $color = '#f59e0b';

            $events[] = [
                'title' => $c->subject,
                'start' => $c->scheduled_at->toIso8601String(),
                'url' => route('campaigns.show', $c->id),
                'backgroundColor' => $color
            ];
        }

        return view('calendar.index', compact('events'));
    }
}
