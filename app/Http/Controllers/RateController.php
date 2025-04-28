<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Http\Requests\StoreRateRequest;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class RateController extends Controller
{
    public function index($id, $type = "designer")
    {
        $type = $type == "designer" ? "designer" : "client";
        $rates = Rate::with(['project', $type])->where($type . "_id", '=', $id);
        return response($rates, 200);
    }

    public function store(Project $project, StoreRateRequest $request)
    {
        if ($project->status == "completed") {
            $request->validated();
            $rate = new Rate();
            $rate->rate = $request->rate;
            $rate->description = $request->description;
            if ($project->client_id == Auth::id()) {
                $rate->designer_id = $rate->project->designer_id;
            } elseif ($project->designer_id == Auth::id()) {
                $rate->client_id = $rate->project->client_id;
            } else {
                return response("not allowed", 403);
            }
            $rate->save();
            return response()->json($rate->load(['project', 'client', 'designer']), 201);
        }
        return response("not allowed", 403);
    }

    public function show(Rate $rate)
    {
        return $rate->load(['project', 'client', 'designer']);
    }

    // public function update(StoreRateRequest $request, Rate $rate)
    // {
    //     $rate->update($request->validated());
    //     return response()->json($rate);
    // }

    // public function destroy(Rate $rate)
    // {
    //     $rate->delete();
    //     return response()->noContent();
    // }
}
