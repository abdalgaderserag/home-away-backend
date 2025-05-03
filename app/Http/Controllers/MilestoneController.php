<?php

namespace App\Http\Controllers;

use App\Enum\Offer\MilestoneStatus;
use App\Enum\Offer\OfferStatus;
use App\Enum\Offer\OfferType;
use App\Enum\Project\Status;
use App\Models\Milestone;
use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\SubmitMilestoneRequest;
use App\Http\Requests\UpdateMilestoneRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use Google\Service\Texttospeech\Turn;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MilestoneController extends Controller
{
    // todo : add authorization
    public function index(Offer $offer)
    {
        if ($offer->user_id == Auth::id() || $offer->project->user_id == Auth::id()) {
            return $offer->milestones();
        }
        return response()->json(['message' => 'You are not unauthorized the offer owner or the project owner'], Response::HTTP_UNAUTHORIZED);
    }

    public function store(StoreMilestoneRequest $request, Offer $offer)
    {
        $sum = $offer->milestones->sum('price');
        $price_left = $offer->price - $sum;

        if ($price_left <= 0) {
            return response()->json([
                'message' => 'You cannot create a new milestone as the total price has reached the offer price'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($price_left < $request->price) {
            return response()->json([
                'message' => "Your total milestones price cannot exceed the offer price.",
                "price_left" => $price_left
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $milestone = $offer->milestones()->create($request->validated());
        $isLastMilestone = $price_left - $request->price == 0;
        if ($isLastMilestone) {
            $offer->status = OfferType::Final->value;
            $offer->update();
            $firstMs = $offer->milestones()->first();
            $firstMs->status = MilestoneStatus::Pending->value;
            $firstMs->update();
        }
        return response()->json(["milestone" => $milestone, "is_last_milestone" => $isLastMilestone], Response::HTTP_CREATED);
    }

    public function show(Offer $offer)
    {
        return response(["offer" => $offer, "milestones" => $offer->milestones()]);
    }

    public function update(UpdateMilestoneRequest $request, Milestone $milestone)
    {
        if ($milestone->offer->project->status !== Status::Published) {
            return response()->json(["message" => "You can't edit milestone right now."]);
        }
        $milestone->update($request->validated());
        return response()->json($milestone);
    }

    public function submit(SubmitMilestoneRequest $request, Milestone $milestone)
    {
        if ($milestone->status !== MilestoneStatus::Pending->value) {
            return response()->json(["message" => "You can't submit this milestone right now."]);
        }
        $milestone->update([
            'attachments' => $request->file('attachments')->store('milestones'),
            'delivery_date' => now(),
            'status' => MilestoneStatus::Reviewing->value,
        ]);
        return response()->json($milestone);
    }

    public function accept(Milestone $milestone)
    {
        if ($milestone->status !== MilestoneStatus::Reviewing) {
            return response()->json(["message" => "this milestone is not submitted by designer yet"], Response::HTTP_NOT_ACCEPTABLE);
        }
        $milestone->update([
            'status' => MilestoneStatus::Completed->value,
        ]);
        $lastMs = $milestone->offer->milestones()->where('status', '!=', MilestoneStatus::Completed)->first();
        if ($lastMs == null) {
            $milestone->offer->update([
                'status' => OfferStatus::Completed->value,
            ]);
            $milestone->offer->project->update([
                'status' => Status::Completed->value,
            ]);
        }
        return response()->json(['milestone' => $milestone, 'is_last_milestone' => $lastMs ? true : false]);
    }

    public function reject(Milestone $milestone)
    {
        if ($milestone->status !== MilestoneStatus::Reviewing) {
            return response()->json(["message" => "this milestone is not submitted by designer yet"], Response::HTTP_NOT_ACCEPTABLE);
        }
        $milestone->update([
            'status' => MilestoneStatus::Pending->value,
        ]);
        return response()->json($milestone);
    }

    public function destroy(Milestone $milestone)
    {
        $milestone->delete();
        return response()->noContent();
    }
}
