<?php

namespace App\Http\Controllers;

use App\Enum\Offer\OfferStatus;
use App\Enum\Offer\OfferType;
use App\Enum\Project\Status;
use App\Models\Offer;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OfferController extends Controller
{
    public function index()
    {
        return Offer::with(['user', 'project.client'])->where("user_id", Auth::id())->paginate();
    }

    public function store(StoreOfferRequest $request)
    {
        $project = Project::find($request->project_id);
        if ($project->status != Status::Published) {
            return response("you can't add offers to this project", Response::HTTP_FORBIDDEN);
        }
        $offer = new Offer($request->validated());
        $offer->status = OfferStatus::Accepted->value;
        $offer->user_id = Auth::id();
        $offer->save();
        return response()->json($offer, Response::HTTP_CREATED);
    }

    public function show(Project $project)
    {
        $offers = $project->offers()->with(["user"])->get();
        $offers->each(function ($offer) {
            $offer->user->rate = $offer->user->rates();
        });
        return response($offers, Response::HTTP_OK);
    }

    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        if (!empty($offer->project->designer_id) && $offer->project->status == Status::InProgress) {
            $offer->update($request->validated());
            return response()->json($offer);
        }
        return response(
            "not allowed to change offer",
            Response::HTTP_FORBIDDEN
        );
    }

    public function accept(Offer $offer)
    {
        if ($offer->project->client_id == Auth::id()) {
            $offer->project->designer_id = $offer->user_id;
            $offer->project->status = Status::InProgress->value;
            $offer->project->save();
            $offers = $offer->project->offers()->where('id', '!=', $offer->id)->get();
            foreach ($offers as $offer) {
                $offer->status = OfferStatus::Declined->value;
                $offer->update();
            }
            return response($offer, Response::HTTP_CREATED);
        }
        return response("this offer doesn't belong to one of your projects", Response::HTTP_FORBIDDEN);
    }

    public function invoice(Offer $offer)
    {
        $invoice = $offer->milestones()->get('attachment');
        return response()->json(['invoice' => $invoice], Response::HTTP_OK);
    }

    public function destroy(Offer $offer)
    {
        if (!empty($offer->project->designer_id)) {
            $offer->delete();
            return response()->noContent();
        }
        return response("not allowed to delete offer", Response::HTTP_FORBIDDEN);
    }
}
