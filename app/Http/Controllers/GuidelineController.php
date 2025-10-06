<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guideline;

class GuidelineController extends Controller
{
    public function index()
    {
        $guidelines = Guideline::paginate(10);
        return view('admin.guidelines.index', compact('guidelines'));
    }

    public function create()
    {
        return view('admin.guidelines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:pnbp,non_pnbp',
            'fee' => 'required|numeric|min:0',
            'required_documents' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        Guideline::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'fee' => $request->fee,
            'required_documents' => $request->required_documents ?? [],
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.guidelines')->with('success', 'Panduan berhasil ditambahkan!');
    }

    public function show(Guideline $guideline)
    {
        return view('admin.guidelines.show', compact('guideline'));
    }

    public function edit(Guideline $guideline)
    {
        return view('admin.guidelines.edit', compact('guideline'));
    }

    public function update(Request $request, Guideline $guideline)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:pnbp,non_pnbp',
            'fee' => 'required|numeric|min:0',
            'required_documents' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $guideline->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'fee' => $request->fee,
            'required_documents' => $request->required_documents ?? [],
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.guidelines')->with('success', 'Panduan berhasil diperbarui!');
    }

    public function destroy(Guideline $guideline)
    {
        $guideline->delete();
        return redirect()->route('admin.guidelines')->with('success', 'Panduan berhasil dihapus!');
    }
}
