<?php

use App\Models\{Offer, Project, User, Milestone};
use App\Enum\Project\Status as ProjectStatus;
use App\Enum\Offer\{OfferStatus, OfferType, MilestoneStatus};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->project = Project::factory()->create(['status' => ProjectStatus::Published, 'client_id' => User::factory()->create()->id]);
});

it('lists offers for the authenticated user', function () {
    Offer::factory()->count(3)->create(['user_id' => $this->user->id]);
    $response = $this->getJson(route('offers.index'));
    $response->assertOk()->assertJsonStructure(['data']);
});

it('prevents storing offer on non-published project', function () {
    $project = Project::factory()->create(['status' => ProjectStatus::Draft]);
    $data = Offer::factory()->make(['project_id' => $project->id])->toArray();
    $response = $this->postJson(route('offers.store'), $data);
    $response->assertForbidden();
});

it('stores an offer successfully', function () {
    $data = Offer::factory()->make(['project_id' => $this->project->id])->toArray();
    $response = $this->postJson(route('offers.store'), $data);
    $response->assertCreated()->assertJsonFragment(['status' => OfferStatus::Pending->value]);
});

it('shows offers to the project owner', function () {
    $client = User::factory()->create();
    $project = Project::factory()->create(['client_id' => $client->id]);
    $this->actingAs($client);
    Offer::factory()->count(2)->create(['project_id' => $project->id]);
    $response = $this->getJson(route('offers.show', $project));
    $response->assertOk();
});

it('forbids showing offers to non-owners', function () {
    $response = $this->getJson(route('offers.show', $this->project));
    $response->assertForbidden();
});

it('updates offer if owner and project not published', function () {
    $offer = Offer::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id]);
    $this->project->update(['status' => ProjectStatus::Draft]);
    $response = $this->putJson(route('offers.update', $offer), ['description' => 'Updated']);
    $response->assertOk()->assertJsonFragment(['description' => 'Updated']);
});

it('rejects updating offer if project is published and owned', function () {
    $offer = Offer::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id]);
    $response = $this->putJson(route('offers.update', $offer), ['description' => 'Should Fail']);
    $response->assertForbidden();
});

it('accepts an offer and updates project/offer states', function () {
    $client = User::factory()->create();
    $project = Project::factory()->create(['client_id' => $client->id]);
    $offer = Offer::factory()->create(['project_id' => $project->id, 'type' => OfferType::Basic->value]);
    Milestone::factory()->create(['offer_id' => $offer->id]);
    $this->actingAs($client);
    $response = $this->postJson(route('offers.accept', $offer));
    $response->assertCreated();
    $this->assertEquals(ProjectStatus::InProgress->value, $offer->project->fresh()->status);
    $this->assertEquals(OfferStatus::Accepted->value, $offer->fresh()->status);
});

it('forbids accepting offers if user is not project owner', function () {
    $offer = Offer::factory()->create(['project_id' => $this->project->id]);
    $response = $this->postJson(route('offers.accept', $offer));
    $response->assertForbidden();
});

it('returns invoice attachments', function () {
    $offer = Offer::factory()->create();
    Milestone::factory()->count(2)->create(['offer_id' => $offer->id, 'attachment' => 'invoice.pdf']);
    $response = $this->getJson(route('offers.invoice', $offer));
    $response->assertOk()->assertJsonStructure(['invoice']);
});

it('deletes offer if project has no designer', function () {
    $offer = Offer::factory()->create(['project_id' => $this->project->id]);
    $response = $this->deleteJson(route('offers.destroy', $offer));
    $response->assertNoContent();
});

it('prevents deleting offer if designer exists', function () {
    $this->project->update(['designer_id' => User::factory()->create()->id]);
    $offer = Offer::factory()->create(['project_id' => $this->project->id]);
    $response = $this->deleteJson(route('offers.destroy', $offer));
    $response->assertForbidden();
});
