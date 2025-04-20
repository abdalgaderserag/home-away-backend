<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Http\Requests\StoreRateRequest;
use App\Http\Requests\UpdateRateRequest;

class RateController extends Controller
{
    public function index()
    {
        return Rate::with(['project', 'client', 'designer'])->paginate();
    }

    public function store(StoreRateRequest $request)
    {
        $rate = Rate::create($request->validated());
        return response()->json($rate->load(['project', 'client', 'designer']), 201);
    }

    public function show(Rate $rate)
    {
        return $rate->load(['project', 'client', 'designer']);
    }

    public function update(StoreRateRequest $request, Rate $rate)
    {
        $rate->update($request->validated());
        return response()->json($rate);
    }

    public function destroy(Rate $rate)
    {
        $rate->delete();
        return response()->noContent();
    }
}
