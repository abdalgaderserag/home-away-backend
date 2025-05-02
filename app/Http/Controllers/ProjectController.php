<?php

namespace App\Http\Controllers;

use App\Enum\Project\Status;
use App\Http\Requests\Project\IndexRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(IndexRequest $request)
    {
        $validated = $request->validated();

        if (empty($validated['sort_by'])) {
            $validated['sort_by'] = "created_at";
        }

        if (empty($validated['sort_order'])) {
            $validated['sort_order'] = "asc";
        }

        // filters
        $query = Project::with(['client'])
            ->where('status', $validated['status'] ?? Status::Published->value)
            ->when($request->filled('search'), function ($q) use ($validated) {
                $q->where('title', 'like', '%' . $validated['search'] . '%');
            })
            ->when($request->filled('min_price'), function ($q) use ($validated) {
                $q->where('min_price', '>=', (float)$validated['min_price']);
            })
            ->when($request->filled('max_price'), function ($q) use ($validated) {
                $q->where('max_price', '<=', (float)$validated['max_price']);
            })
            ->when($request->filled('unit_type'), function ($q) use ($validated) {
                $q->where('unit_type', $validated['unit_type']);
            })
            ->when($request->filled('location'), function ($q) use ($validated) {
                $q->where('location', $validated['location']);
            })
            ->when($request->filled('skill'), function ($q) use ($validated) {
                $q->where('skill', $validated['skill']);
            });

        //sorting
        $sortBy = in_array(
            $validated['sort_by'] ?? 'created_at',
            ['created_at', 'title', 'min_price', 'max_price', 'deadline']
        )
            ? $validated['sort_by']
            : 'created_at';

        $sortOrder = in_array(strtolower($validated['sort_order'] ?? 'asc'), ['asc', 'desc'])
            ? strtolower($validated['sort_order'])
            : 'asc';

        $query->orderBy($sortBy, $sortOrder);

        //pagination
        $perPage = min($validated['per_page'] ?? 10, 100);

        return response()->json($query->paginate($perPage));
    }

    public function projects($status = "")
    {
        $projects = Project::with('offers')->where('client_id', Auth::id());
        if ($status !== "") {
            $projects = $projects->where("status", $status);
        }
        return response($projects);
    }

    public function create()
    {
        $project = new Project();
        $project->client_id = Auth::id();
        $project->status = Status::Draft;
        $project->save();
        return response()->json($project, 201);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        if ($this->validateOwner($project->client_id) && $project->status !== Status::Draft->value) {
            return response(["message" => "you are not allowed to edit this project"], 403);
        }
        $project->update($request->validated());
        $project->refresh();
        return response()->json($project);
    }

    public function save(StoreProjectRequest $request, Project $project)
    {
        if ($this->validateOwner($project->client_id) && $project->status !== Status::Draft->value) {
            return response(["message" => "you are not allowed to edit this project"], 403);
        }
        $project->status = Status::Pending->value;
        $project->update($request->validated());
    }

    public function show(Project $project)
    {
        if ($project->status !== Status::Published->value) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return $project->load(['client', 'offers.user.rate']);
    }

    public function destroy(Project $project)
    {
        if ($this->validateOwner($project->client_id))
            return response("you are not the project owner", 403);

        if (in_array($project->status, [
            Status::InProgress->value,
            Status::Completed->value
        ])) {
            return response()->json([
                'message' => 'Cannot delete project in current state'
            ], 403);
        }
        $project->delete();
        return response()->noContent();
    }

    private function validateOwner($id)
    {
        return !($id === Auth::id());
    }
}
