<?php

namespace App\Http\Controllers;

use App\Enum\Project\Status;
use App\Http\Requests\Project\IndexRequest;
use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(IndexRequest $request)
    {
        $query = Project::with(['client'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            }, function ($q) {
                $q->where('status', Status::Published->value);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('min_price'), function ($q) use ($request) {
                $q->where('min_price', '>=', $request->min_price);
            })
            ->when($request->filled('max_price'), function ($q) use ($request) {
                $q->where('max_price', '<=', $request->max_price);
            })
            ->when($request->filled('unit_type'), function ($q) use ($request) {
                $q->where('unit_type', $request->unit_type);
            })
            ->when($request->filled('location'), function ($q) use ($request) {
                $q->where('location', $request->location);
            })
            ->when($request->filled('skill'), function ($q) use ($request) {
                $q->where('skill', $request->skill);
            });

        // Sorting
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->per_page ?? 10;

        return $query->paginate($perPage);
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->validated());
        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        return $project->load(['client', 'designer']);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        if ($this->validateOwner($project->id))
            return response("you are not the project owner", 403);

        $project->update($request->validated());
        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        if ($this->validateOwner($project->id))
            return response("you are not the project owner", 403);

        $project->delete();
        return response()->noContent();
    }

    private function validateOwner($id)
    {
        return ($id === Auth::id());
    }
}
