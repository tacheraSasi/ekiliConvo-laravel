<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class InsightController extends Controller
{
    // Fetch insights for homepage
    public function home()
    {
        // Fetch insights with pagination (5 insights per page)
        $insights = Insight::orderBy('created_at', 'desc')->paginate(6);
        
        // Pass the insights to the view
        return view('home', compact('insights'));
    }

    // Fetch all insights
    public function index()
    {
        $insights = Insight::with('category', 'user', 'likes', 'comments')->latest()->paginate(6);
        return view('home', compact('insights'));
    }

    // Show the form to create a new insight
    public function create()
    {
        $categories = Category::all();
        return view('insights.write', compact('categories'));
    }

    // Store a new insight
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required', // Ensure this field exists in the form
            'category_id' => 'required|exists:categories,id',
        ]);

        $slug = Str::slug($request->title);

        Insight::create([
            'title' => $request->title,
            'content' => $request->content, // Ensure this is being passed correctly
            'slug' => $slug,
            'category_id' => $request->category_id,
            'user_id' => Auth::id(), // Use Auth::user() for the authenticated user ID
        ]);

        return redirect()->route('insights.index')->with('success', 'Insight created successfully.');
    }

    // Show a specific insight
    public function show($slug)
    {
        $insight = Insight::where('slug', $slug)->with('category', 'user', 'comments', 'likes')->firstOrFail();
        return view('insights.show', compact('insight'));
    }

    // Show the form to edit an insight
    public function edit($id)
    {
        $insight = Insight::findOrFail($id);
        $categories = Category::all();
        return view('insights.edit', compact('insight', 'categories'));
    }

    // Update an existing insight
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required', // Ensure this field exists in the form
            'category_id' => 'required|exists:categories,id',
        ]);

        $insight = Insight::findOrFail($id);
        $slug = Str::slug($request->title);

        $insight->update([
            'title' => $request->title,
            'content' => $request->content, // Ensure this is being passed correctly
            'slug' => $slug,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('insights.index')->with('success', 'Insight updated successfully.');
    }

    // Delete an insight
    public function destroy($id)
    {
        $insight = Insight::findOrFail($id);
        if (Auth::user() != $insight->user){
            return redirect(request()->url());
        }
        $insight->delete();
        return redirect()->route('insights.index')->with('success', 'Insight deleted successfully.');
    }
}
