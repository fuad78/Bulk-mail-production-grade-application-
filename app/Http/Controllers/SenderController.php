<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $senders = \App\Models\Sender::latest()->paginate(10);
        return view('admin.senders.index', compact('senders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.senders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255', // Ideally unique, but multiple departments might share
        ]);

        \App\Models\Sender::create($request->all());

        return redirect()->route('senders.index')->with('success', 'Sender created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $sender = \App\Models\Sender::findOrFail($id);
        return view('admin.senders.edit', compact('sender'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sender = \App\Models\Sender::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $sender->update($request->all());

        return redirect()->route('senders.index')->with('success', 'Sender updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sender = \App\Models\Sender::findOrFail($id);
        $sender->delete();
        return redirect()->route('senders.index')->with('success', 'Sender deleted successfully.');
    }
}
