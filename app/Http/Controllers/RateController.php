<?php

namespace App\Http\Controllers;

use App\Enum\Project\Status;
use App\Models\Rate;
use App\Http\Requests\StoreRateRequest;
use App\Models\Project;
use App\Models\User;
use App\Notifications\Rated;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

use function Pest\Laravel\get;

class RateController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'type' => 'required|in:client,designer',
        ]);
        $rates = Rate::with(['project', $request->type])->where($request->type . "_id", $request->id)->get();
        return response()->json(['rate' => Auth::user()->rates(), "rates" => $rates], Response::HTTP_OK);
    }

    public function store(Project $project, StoreRateRequest $request)
    {
        $isClient = $project->client_id == Auth::id();
        $isDesigner = $project->designer_id == Auth::id();
        if (!$isClient && !$isDesigner) {
            return response(["message" => "You are not part of this project and can't rate."], Response::HTTP_UNAUTHORIZED);
        }
        $old_rate = Rate::where('project_id', $project->id);

        if ($isClient) {
            $old_rate = $old_rate->where('designer_id', $project->designer_id)->first();
        } elseif ($isDesigner) {
            $old_rate = $old_rate->where('client_id', $project->client_id)->first();
        }

        if (!empty($old_rate))
            return response(["message" => "You have already added rating to this project"], Response::HTTP_UNAUTHORIZED);

        if ($project->status == Status::Completed) {
            $request->validated();
            $rate = new Rate();
            $rate->rate = $request->rate;
            $rate->description = $request->description;
            $rate->project_id = $project->id;
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
