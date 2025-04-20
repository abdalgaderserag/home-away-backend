<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        return Project::with(['client', 'designer'])->paginate();
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
