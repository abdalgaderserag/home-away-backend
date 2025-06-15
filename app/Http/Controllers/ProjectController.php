<?php

namespace App\Http\Controllers;

use App\Enum\Project\Status;
use App\Http\Requests\Project\IndexRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\User;
use App\Notifications\Project\ProjectSentForApproval;
use Coderflex\LaravelTicket\Models\Category;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class ProjectController extends Controller
{
    public function index(IndexRequest $request)
    {
        $validated = $request->validated();

        if (empty($validated['sort_by'])) {
            $validated['sort_by'] = "published_at";
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
            $validated['sort_by'] ?? 'published_at',
            ['created_at', 'title', 'min_price', 'max_price', 'deadline']
        )
            ? $validated['sort_by']
            : 'published_at';

        $sortOrder = in_array(strtolower($validated['sort_order'] ?? 'asc'), ['asc', 'desc'])
            ? strtolower($validated['sort_order'])
            : 'asc';

        $query->orderBy($sortBy, $sortOrder);

        //pagination
        $perPage = min($validated['per_page'] ?? 10, 100);

        return response()->json($query->paginate($perPage));
    }

    public function projects($status = "", $perPage = 10, $page = 1)
    {
        $projects = Project::with('offers')->where('client_id', Auth::id());
        if ($status !== "") {
            $projects = $projects->where("status", $status);
        }
        return response()->json(["projects" => $projects->paginate($perPage, ['*'], 'page', $page)]);
    }

    public function create()
    {
        $project = new Project();
        $project->client_id = Auth::id();
        $project->status = Status::Draft->value;
        $project->save();
        return response()->json(["project" => $project], Response::HTTP_CREATED);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        if ($project->client_id !== Auth::id() && $project->status !== Status::Draft->value) {
            return response(["message" => "you are not allowed to edit this project"], 403);
        }

        $project->update($request->validated());
        $project->refresh();
        $attachments = collect();
        foreach ($request->attachment ? $request->attachment : [] as $attach) {
            $attachment = Attachment::find($attach);
            $attachment->project_id = $project->id;
            $attachment->update();
            $attachments->push($attachment);
        }
        return response()->json(["project" => $project, "attachments" => $attachments], Response::HTTP_OK);
    }

    public function save(StoreProjectRequest $request, Project $project)
    {
        $client = User::find(Auth::id());
        if ($project->client_id !== $client->id && $project->status !== Status::Draft->value) {
            return response(["message" => "you are not allowed to edit this project"], 403);
        }
        $project->status = Status::Pending->value;
        $project->update($request->validated());
        
        $ticket = $client->tickets()->create([
            'title' => $request->title,
            'message' => $project->id,
        ]);
        $category = Category::where('slug', 'project-approval')->first();
        $ticket->attachCategories($category);
        $client->notify(new ProjectSentForApproval($project));
        return response()->json([
            'project' => $project->refresh(),
            'attachments' => Attachment::where('project_id', $project->id)->get(),
        ], Response::HTTP_CREATED);
    }

    public function show(Project $project)
    {
        if ($project->client_id == Auth::id() || $project->designer_id == Auth::id()) {
        } elseif ($project->status !== Status::Published->value && $project->client_id !== Auth::id()) {
            return response()->json(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $project->load(['client', 'offers.user']);

        $project->offers->each(function ($offer) {
            $offer->user->rate = $offer->user->rates();
        });

        return response(["project" => $project]);
    }

    public function destroy(Project $project)
    {
        if ($project->client_id !== Auth::id())
            return response(["message" => "you are not the project owner"], 403);

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
}
