<?php

namespace App\Http\Controllers;

use App\Models\Recipient;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function index()
    {
        // Ensure only admin can view
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $logs = Recipient::with(['campaign', 'campaign.sender'])
            ->latest()
            ->paginate(20);

        return view('admin.email_logs.index', compact('logs'));
    }
}
