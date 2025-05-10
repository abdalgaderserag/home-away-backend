<?php

namespace App\Http\Controllers;

use App\Enum\Project\Status;
use App\Models\Rate;
use App\Http\Requests\StoreRateRequest;
use App\Models\Project;
use App\Notifications\Rated;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RateController extends Controller
{
    public function index($id, $type = "designer")
    {
        $type = $type == "designer" ? "designer" : "client";
        $rates = Rate::with(['project', $type])->where($type . "_id", $id)->get();
        return response()->json(["rates" => $rates], Response::HTTP_OK);
    }

    public function store(Project $project, StoreRateRequest $request)
    {
        if ($project->client_id !== Auth::id() || $project->designer_id !== Auth::id()) {
            return response(["message" => "You are not part of this project and can't rate."], Response::HTTP_UNAUTHORIZED);
        }

        if ($project->status == Status::Completed->value) {
            $request->validated();
            $rate = new Rate();
            $rate->rate = $request->rate;
            $rate->description = $request->description;
            if ($project->client_id == Auth::id()) {
                $rate->designer_id = $project->designer_id;
                $designer = $rate->designer;
                $designer->notify(new Rated(Auth::user()));
            } elseif ($project->designer_id == Auth::id()) {
                $rate->client_id = $project->client_id;
                $client = $project->client;
                $client->notify(new Rated(Auth::user()));
            } else {
                return response(["message" => "not allowed"], Response::HTTP_FORBIDDEN);
            }
            $rate->save();
            return response()->json(['rate' => $rate->load(['project', 'client', 'designer'])], Response::HTTP_CREATED);
        }

        return response(["message" => "not allowed"], Response::HTTP_FORBIDDEN);
    }

    public function show(Rate $rate)
    {
        return response(['rate' => $rate->load(['project', 'client', 'designer'])], Response::HTTP_OK);
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
