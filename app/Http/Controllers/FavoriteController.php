<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $favorites = Auth::user()->favorites()->with('project')->get();
        return response($favorites, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $f = Favorite::where('user_id', Auth::id())
            ->where('project_id', $request->project_id)
            ->first();

        if ($f) {
            $f->delete();
            return response()->json([
                'message' => 'Project removed from favorites.',
                'project_id' => $f->project_id,
            ], Response::HTTP_OK);
        }

        $favorite = new Favorite();
        $favorite->user_id = Auth::id();
        $favorite->project_id = $request->project_id;
        $favorite->save();

        return response()->json([
            'message' => 'Project added to favorites.',
            'project_id' => $favorite->project_id,
        ], Response::HTTP_CREATED);
    }
}
