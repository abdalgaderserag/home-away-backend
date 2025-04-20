<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;

class OfferController extends Controller
{
    public function index()
    {
        return Offer::with(['user', 'project'])->paginate();
    }

    public function store(StoreOfferRequest $request)
    {
        $offer = Offer::create($request->validated());
        return response()->json($offer, 201);
    }

    public function show(Offer $offer)
    {
        return $offer->load(['user', 'project']);
    }

    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        $offer->update($request->validated());
        return response()->json($offer);
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return response()->noContent();
    }
}
