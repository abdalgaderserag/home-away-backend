<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Http\Requests\StoreMilestoneRequest;
use App\Http\Requests\UpdateMilestoneRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Offer;
use Symfony\Component\HttpFoundation\Response;

class MilestoneController extends Controller
{
    public function index(Offer $offer)
    {
        return $offer->milestones();
    }

    public function store(StoreMilestoneRequest $request, Offer $offer)
    {
        $milestone = $offer->milestones()->create($request->validated());
        return response()->json($milestone, Response::HTTP_CREATED);
    }

    public function show(Offer $offer, Milestone $milestone)
    {
        return $milestone->load('offer');
    }

    public function update(UpdateOfferRequest $request, Offer $offer, Milestone $milestone)
    {
        $milestone->update($request->validated());
        return response()->json($milestone);
    }

    public function destroy(Offer $offer, Milestone $milestone)
    {
        $milestone->delete();
        return response()->noContent();
    }
}
