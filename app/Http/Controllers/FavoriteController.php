<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $favorites = Auth::user()->favorites()->with('project');
        return view('favorites.index', compact('favorites'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $favorite = new Favorite();
        $favorite->user_id = Auth::id();
        $favorite->project_id = $request->project_id;
        $favorite->save();

        return redirect()->back()->with('success', 'Project added to favorites.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Favorite $favorite)
    {
        if ($favorite->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $favorite->delete();

        return redirect()->back()->with('success', 'Project removed from favorites.');
    }
}
