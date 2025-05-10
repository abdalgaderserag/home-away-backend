<?php

namespace App\Http\Controllers;

use App\Enum\Offer\MilestoneStatus;
use App\Enum\Offer\OfferStatus;
use App\Enum\Offer\OfferType;
use App\Enum\Project\Status;
use App\Models\Offer;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Milestone;
use App\Models\Project;
use App\Notifications\Offer\AcceptedOffer;
use App\Notifications\Offer\ReceivedOffer;
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
            return response(["message" => "you can't add offers to this project"], Response::HTTP_FORBIDDEN);
        }
        if ($project->client_id === Auth::id())
            return response(["message" => "you can't add offer to your own project"], Response::HTTP_FORBIDDEN);
        $offer = new Offer($request->validated());
        $offer->status = OfferStatus::Pending->value;
        $offer->user_id = Auth::id();
        $offer->save();
        $client = $project->client;
        $client->notify(new ReceivedOffer($offer));
        return response()->json(["offer" => $offer], Response::HTTP_CREATED);
    }

    public function show(Project $project)
    {
        if ($project->client->id !== Auth::id()) {
            return response(["message" => "not allowed to see project details"], Response::HTTP_FORBIDDEN);
        }
        $offers = $project->offers()->with(["user"])->get();
        $offers->each(function ($offer) {
            $offer->user->rate = $offer->user->rates();
        });
        return response(["offers" => $offers], Response::HTTP_OK);
    }

    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        if ($offer->user_id == Auth::id() && $offer->project->status === Status::Published->value) {
            $offer->update($request->validated());
            return response()->json(["offer" => $offer]);
        }
        return response(
            "not allowed to change offer",
            Response::HTTP_FORBIDDEN
        );
    }

    public function accept(Offer $offer)
    {
        if ($offer->project->client_id !== Auth::id())
            return response(["message" => "You are not Authorized to accept offers for this project."], Response::HTTP_FORBIDDEN);
        if ($offer->project->client_id == Auth::id()) {
            if ($offer->type == OfferType::Basic) {
                return response("The designer needs to add milestones to the offer", Response::HTTP_BAD_REQUEST);
                $offer->price = $offer->project->price;
            }
            $fM = $offer->milestones->first();
            $fM->status = MilestoneStatus::Pending->value;
            $offer->project->designer_id = $offer->user_id;
            $offer->project->status = Status::InProgress->value;
            $offer->project->save();
            $offer->status = OfferStatus::Accepted->value;
            $offer->update();
            $offers = $offer->project->offers()->where('id', '!=', $offer->id)->get();
            foreach ($offers as $otherOffer) {
                $otherOffer->status = OfferStatus::Declined->value;
                $otherOffer->update();
            }
            $designer = $offer->user;
            $designer->notify(new AcceptedOffer($offer));
            return response(["offer" => $offer], Response::HTTP_CREATED);
        }
        return response("this offer doesn't belong to one of your projects", Response::HTTP_FORBIDDEN);
    }

    public function invoice(Offer $offer)
    {
        if ($offer->user_id === Auth::id() || $offer->project->client_id === Auth::id()) {
            $invoice = $offer->milestones()->get('attachment');
            return response()->json(['invoice' => $invoice], Response::HTTP_OK);
        }
        return response(["message" => "You don't have access to invoice of this project"], Response::HTTP_FORBIDDEN);
    }

    public function destroy(Offer $offer)
    {
        if ($offer->user_id !== Auth::id()) {
            return response(["message" => "this offer doesn't belong to you and you can't delete it"], Response::HTTP_FORBIDDEN);
        }
        if ($offer->project->designer_id === Auth::id()) {
            $offer->delete();
            return response()->noContent();
        }
        return response(["message" => "not allowed to delete offer after"], Response::HTTP_FORBIDDEN);
    }
}
