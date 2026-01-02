<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactList;
use App\Models\Contact;
use App\Services\CsvImportService;

class ContactListController extends Controller
{
    protected $csvImportService;

    public function __construct(CsvImportService $csvImportService)
    {
        $this->csvImportService = $csvImportService;
    }

    public function index(Request $request)
    {
        $query = ContactList::with('department', 'user')->withCount('contacts');

        if (!$request->user()->isAdmin()) {
            $query->where('department_id', $request->user()->department_id);
        }

        $lists = $query->latest()->paginate(20);

        return view('lists.index', compact('lists'));
    }

    public function create()
    {
        if (request()->user()->isViewer())
            abort(403);
        return view('lists.create');
    }

    public function store(Request $request)
    {
        if (request()->user()->isViewer())
            abort(403);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:csv,txt|max:10240', // 10MB
        ]);

        $list = ContactList::create([
            'name' => $request->name,
            'description' => $request->description,
            'department_id' => $request->user()->department_id ?? \App\Models\Department::first()->id, // Fallback for Super Admin
            'user_id' => $request->user()->id,
        ]);

        $count = 0;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->getRealPath();
            $count = $this->csvImportService->importToList($list, $path);
        }

        return redirect()->route('lists.index')->with('success', "List created with $count contacts.");
    }

    public function show(ContactList $list)
    {
        // Auth check
        if (!auth()->user()->isAdmin() && auth()->user()->department_id !== $list->department_id) {
            abort(403);
        }

        $contacts = $list->contacts()->paginate(50);
        return view('lists.show', compact('list', 'contacts'));
    }

    public function destroy(ContactList $list)
    {
        if (request()->user()->isViewer())
            abort(403);
        if (!auth()->user()->isAdmin() && auth()->user()->department_id !== $list->department_id) {
            abort(403);
        }

        $list->delete();

        return redirect()->route('lists.index')->with('success', 'List deleted successfully.');
    }
}
