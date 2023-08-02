<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\InertiaTest;

class InertiaTestController extends Controller
{
    public function index()
    {
        return Inertia::render('Inertia/index', [
            'blogs' => InertiaTest::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Inertia/create');
    }

    public function show($id)
    {
        return Inertia::render('Inertia/show', [
            'id' => $id,
            'blog' => InertiaTest::findOrFail($id),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'max:20'],
            'content' => ['required']
        ]);
        $inertiaTest = new InertiaTest;
        $inertiaTest->title = $request->title;
        $inertiaTest->content = $request->content;
        $inertiaTest->save();
        return to_route('inertia.index')->with(['message' => 'data stored.']);
    }

    public function delete($id)
    {
        $book = InertiaTest::find($id);
        $book->delete();
        return to_route('inertia.index')->with(['message' => 'data deleted.']);
    }
}
