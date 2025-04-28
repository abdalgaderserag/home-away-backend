<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    public function index()
    {
        return Offer::with(['user', 'project'])->where("user_id", Auth::id())->paginate();
    }

    public function store(StoreOfferRequest $request)
    {
        $project = Project::findOne($request->project_id);
        if ($project->status != "published") {
            return response("you can't add offers to this project", 403);
        }
        $offer = new Offer($request->validated());
        $offer->user_id = Auth::id();
        $offer->save();
        return response()->json($offer, 201);
    }

    public function show(Project $project)
    {
        $offers = $project->offers()->with("user")->get();
        return response($offers, 200);
    }

    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        if (!empty($offer->project->designer_id)) {
            $offer->update($request->validated());
            return response()->json($offer);
        }
        return response("not allowed to change offer", 403);
    }

    public function accept(Offer $offer)
    {
        if ($offer->project->client_id == Auth::id()) {
            $offer->project->designer_id = $offer->user_id;
            $offer->project->status = "in_progress";
            $offer->project->save();
            return response($offer, 201);
        }
        return response("this offer doesn't belong to one of your projects", 403);
    }

    public function destroy(Offer $offer)
    {
        if (!empty($offer->project->designer_id)) {

            $offer->delete();
            return response()->noContent();
        }
        return response("not allowed to delete offer", 403);
    }
}
