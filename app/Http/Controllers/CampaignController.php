<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Services\CampaignService;
use App\Services\CsvImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CampaignController extends Controller
{
    protected $campaignService;
    protected $csvImportService;

    public function __construct(CampaignService $campaignService, CsvImportService $csvImportService)
    {
        $this->campaignService = $campaignService;
        $this->csvImportService = $csvImportService;
    }

    public function indexView(Request $request)
    {
        $query = Campaign::with('department', 'user');

        if (!$request->user()->isAdmin()) {
            $query->where('department_id', $request->user()->department_id);
        }

        $campaigns = $query->latest()->paginate(20);

        return view('campaigns.index', compact('campaigns'));
    }

    public function index(Request $request)
    {
        $query = Campaign::with('department', 'user');

        if (!$request->user()->isAdmin()) {
            $query->where('department_id', $request->user()->department_id);
        }

        return response()->json($query->paginate(20));
    }

    public function create()
    {
        if (request()->user()->isViewer())
            abort(403);
        $senders = \App\Models\Sender::all();
        return view('campaigns.create', compact('senders'));
    }

    public function storeWeb(Request $request)
    {
        if ($request->user()->isViewer())
            abort(403);
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'sender_id' => 'nullable|exists:senders,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $this->campaignService->createCampaign($request->user(), $validated);

        return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function store(Request $request)
    {
        if ($request->user()->isViewer())
            abort(403);
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $campaign = $this->campaignService->createCampaign($request->user(), $validated);

        return response()->json($campaign, 201);
    }

    public function uploadRecipients(Request $request, Campaign $campaign)
    {
        if ($request->user()->isViewer())
            abort(403);
        // Simple authorization check
        if ($request->user()->id !== $campaign->user_id && !$request->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB
        ]);

        $path = $request->file('file')->getRealPath();
        $count = $this->csvImportService->import($campaign, $path);

        return response()->json(['message' => "Imported $count recipients"]);
    }

    public function submit(Request $request, Campaign $campaign)
    {
        if ($request->user()->isViewer())
            abort(403);
        $this->campaignService->submitForApproval($request->user(), $campaign);
        return response()->json(['message' => 'Campaign submitted for approval']);
    }

    public function approve(Request $request, Campaign $campaign)
    {
        if ($request->user()->isViewer())
            abort(403);
        $this->campaignService->approve($request->user(), $campaign);
        return response()->json(['message' => 'Campaign approved']);
    }

    public function reject(Request $request, Campaign $campaign)
    {
        if ($request->user()->isViewer())
            abort(403);
        $request->validate(['reason' => 'required|string']);
        $this->campaignService->reject($request->user(), $campaign, $request->reason);
        return response()->json(['message' => 'Campaign rejected']);
    }
    public function show(Campaign $campaign)
    {
        $campaign->load('department', 'user');

        $listsQuery = \App\Models\ContactList::query();
        if (!auth()->user()->isAdmin()) {
            $listsQuery->where('department_id', auth()->user()->department_id);
        }
        $availableLists = $listsQuery->get();

        return view('campaigns.show', compact('campaign', 'availableLists'));
    }

    public function uploadRecipientsWeb(Request $request, Campaign $campaign)
    {
        if ($request->user()->isViewer())
            abort(403);
        // Simple authorization check
        if ($request->user()->id !== $campaign->user_id && !$request->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB
        ]);

        $path = $request->file('file')->getRealPath();
        $count = $this->csvImportService->import($campaign, $path);

        return redirect()->back()->with('success', "Imported $count recipients.");
    }

    public function submitWeb(Request $request, Campaign $campaign)
    {
        if ($request->user()->isViewer())
            abort(403);
        try {
            $this->campaignService->submitForApproval($request->user(), $campaign);
            return redirect()->back()->with('success', 'Campaign submitted for approval.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function importList(Request $request, Campaign $campaign)
    {
        if ($request->user()->isViewer())
            abort(403);
        // Simple authorization check
        if ($request->user()->id !== $campaign->user_id && !$request->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'contact_list_id' => 'required|exists:contact_lists,id',
        ]);

        // Check if list belongs to same department (if not admin)
        // Strictly speaking, if they can see the list ID in the dropdown, it should be valid,
        // but let's be safe.
        $list = \App\Models\ContactList::find($request->contact_list_id);
        if (!$request->user()->isAdmin() && $list->department_id !== $request->user()->department_id) {
            abort(403);
        }

        $count = $this->campaignService->importContactsFromList($campaign, $request->contact_list_id);

        return redirect()->back()->with('success', "Imported $count recipients from Address Book.");
    }

    public function approveWeb(Request $request, Campaign $campaign)
    {
        if ($request->user()->isViewer())
            abort(403);
        try {
            $this->campaignService->approve($request->user(), $campaign);
            return redirect()->back()->with('success', 'Campaign approved and queued for sending.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function rejectWeb(Request $request, Campaign $campaign)
    {
        if ($request->user()->isViewer())
            abort(403);
        $request->validate(['reason' => 'required|string']);

        try {
            $this->campaignService->reject($request->user(), $campaign, $request->reason);
            return redirect()->back()->with('success', 'Campaign rejected.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function destroyWeb(Request $request, Campaign $campaign)
    {
        if ($request->user()->isViewer())
            abort(403);
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted successfully.');
    }

    public function retryFailed(Request $request, Campaign $campaign)
    {
        if ($request->user()->isViewer())
            abort(403);
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $count = $campaign->recipients()->where('status', 'failed')->update(['status' => 'pending']);

        if ($count > 0) {
            $campaign->update(['status' => \App\Models\Campaign::STATUS_SENDING]);
            \App\Jobs\SendCampaignJob::dispatch($campaign);
            return redirect()->back()->with('success', "Retrying $count failed emails.");
        }

        return redirect()->back()->with('info', 'No failed emails to retry.');
    }
}
