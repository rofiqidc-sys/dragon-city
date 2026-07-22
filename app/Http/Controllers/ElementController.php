<?php

namespace App\Http\Controllers;

use App\Models\Element;
use Illuminate\Http\Request;

class ElementController extends Controller
{
    public function index()
    {
        $elements = Element::latest()->get();

        return view('elements.index', compact('elements'));
    }

    public function create()
    {
        return view('elements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
        ]);

        Element::create($request->only(['name', 'alias']));

        return redirect()->route('elements.index')->with('success', 'Element created successfully.');
    }

    public function edit(Element $element)
    {
        return view('elements.edit', compact('element'));
    }

    public function update(Request $request, Element $element)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
        ]);

        $element->update($request->only(['name', 'alias']));

        return redirect()->route('elements.index')->with('success', 'Element updated successfully.');
    }

    public function destroy(Element $element)
    {
        $element->delete();

        return redirect()->route('elements.index')->with('success', 'Element deleted successfully.');
    }
}
