<?php

use App\Enum\Project\Status;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory([
        "type" => 'client'
    ])->create();

    actingAs($this->user);
});

it('can list projects with default sorting', function () {
    Project::factory()->count(3)->create([
        'status' => Status::Published->value,
        'client_id' => $this->user->id,
    ]);
    getJson(route('projects.index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta']);
});

it('can list client projects with status filter', function () {
    Project::factory()->create(['client_id' => $this->user->id, 'status' => Status::Draft->value]);
    Project::factory()->create(['client_id' => $this->user->id, 'status' => Status::Published->value]);

    getJson(route('projects.projects', [Status::Published->value, 10, 1]))
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('can create a draft project', function () {
    postJson(route('projects.create'))
        ->assertCreated()
        ->assertJsonFragment(['status' => Status::Draft->value, 'client_id' => $this->user->id]);
});

it('restricts update when not owner or not draft', function () {
    $project = Project::factory()->create(['status' => Status::Published->value]);

    putJson(route('projects.update', $project), ['title' => 'New Title'])
        ->assertForbidden();
});

it('updates project with file upload when owner and draft', function () {
    $project = Project::factory()->create(['client_id' => $this->user->id, 'status' => Status::Draft->value]);
    $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

    putJson(route('projects.update', $project), [
        'title' => 'Updated',
        'attachment' => $file,
    ])->assertOk()
        ->assertJsonFragment(['title' => 'Updated']);

    Storage::disk('public')->assertExists("project/{$project->id}/" . $file->hashName());
});

it('returns file not found on save if attachment missing', function () {
    $project = Project::factory()->create(['client_id' => $this->user->id, 'status' => Status::Draft->value]);
    postJson(route('projects.save', $project), [
        'title' => 'Test',
        'description' => 'Desc',
        'unit_type' => 'unit',
        'space' => 10,
        'location' => 'City',
        'deadline' => now()->addDay()->toDateString(),
        'min_price' => 100,
        'max_price' => 200,
        'resources' => false,
        'skill' => 'skill',
        'attachment' => ['missing.pdf'],
    ])->assertNotFound()
        ->assertJsonFragment(['message' => 'File not found']);
});

it('saves project and returns created when valid', function () {
    $project = Project::factory()->create(['client_id' => $this->user->id, 'status' => Status::Draft->value]);
    $path = 'project/attachments/doc.pdf';
    Storage::disk('public')->put($path, 'dummy');

    postJson(route('projects.save', $project), array_merge(
        Project::factory()->raw(['attachment' => []]),
        ['attachment' => [$path]]
    ))->assertCreated()
        ->assertJsonStructure(['project']);
});

it('shows project for published and owner', function () {
    $project = Project::factory()->create(['status' => Status::Published->value]);
    getJson(route('projects.show', $project))
        ->assertOk()
        ->assertJsonStructure(['client', 'offers']);
});

it('does not show private project to other users', function () {
    $other = User::factory()->create();
    $project = Project::factory()->create(['status' => Status::Draft->value, 'client_id' => $other->id]);

    getJson(route('projects.show', $project))
        ->assertNotFound();
});

it('destroys project when owner and not in progress or completed', function () {
    $project = Project::factory()->create(['client_id' => $this->user->id, 'status' => Status::Draft->value]);
    deleteJson(route('projects.destroy', $project))
        ->assertNoContent();
});

it('cannot destroy project not owned or in progress', function () {
    $project = Project::factory()->create(['status' => Status::InProgress->value]);
    deleteJson(route('projects.destroy', $project))
        ->assertForbidden();
});
