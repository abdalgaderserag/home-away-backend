<?php

namespace App\Http\Controllers;

use App\Enum\Project\Status;
use App\Http\Requests\Project\IndexRequest;
use App\Models\Project;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(IndexRequest $request)
    {
        $validated = $request->validated();

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

    public function create()
    {
        $pro = Project::where('status', "draft")->first();
        if (empty($pro)) {
            return response()->json($pro, 200);
        }
        $project = new Project();
        $project->status = "draft";
        $project->save();
        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        if ($project->status == "published") {
            return $project->load(['client', 'offers.user.rate']);
        }
        return response("the project you are looking for is not found", 404);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        if ($this->validateOwner($project->client_id))
            return response("you are not the project owner", 403);

        try {
            $project->update($request->validated());
            $project->status = "pending";
            $project->save();
        } catch (ValidationException $error) {
            $project->status = "draft";
            $project->save();
        }
        return response()->json($project, 200);
    }

    public function destroy(Project $project)
    {
        if ($this->validateOwner($project->client_id))
            return response("you are not the project owner", 403);
        switch ($project->status) {
            case 'in_progress':
            case 'completed':
                // todo : make the delete unless agree both
                return response("not allowed", 401);
                break;
            default:
                $project->delete();
                return response()->noContent();
                break;
        }
    }

    private function validateOwner($id)
    {
        return !($id === Auth::id());
    }
}
