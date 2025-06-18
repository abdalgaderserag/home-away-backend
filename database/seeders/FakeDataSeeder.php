<?php

namespace Database\Seeders;

use App\Enum\Offer\MilestoneStatus;
use App\Enum\Offer\OfferStatus;
use App\Enum\Offer\OfferType;
use App\Enum\Project\Status;
use App\Models\Milestone;
use App\Models\Offer;
use App\Models\Project;
use App\Models\User;
use App\Traits\TicketInitTrait;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder
{
    use TicketInitTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allUsers = User::factory(10)->create();

        foreach ($allUsers as $currentUser) {
            if ($currentUser->type === 'client' && $currentUser->id !== 5) {
                $designerUsers = $allUsers->where('type', 'designer');

                Project::factory(rand(0, 3))->create([
                    'client_id' => $currentUser->id,
                    'status' => Status::Published,
                ])->each(function (Project $project) use ($designerUsers) {
                    foreach ($designerUsers as $designer) {
                        Offer::factory()->create([
                            'user_id' => $designer->id,
                            'project_id' => $project->id,
                        ]);
                    }
                });


                for ($i = 0; $i < rand(1, 3); $i++) {
                    $chosenDesigner = $designerUsers->random();

                    $inProgressProject = Project::factory()->create([
                        'client_id' => $currentUser->id,
                        'designer_id' => $chosenDesigner->id,
                        'status' => Status::InProgress
                    ]);

                    foreach ($designerUsers as $designer) {
                        if ($designer->id !== $chosenDesigner->id) {
                            Offer::factory()->create([
                                'user_id' => $designer->id,
                                'project_id' => $inProgressProject->id,
                                'status' => OfferStatus::Declined
                            ]);
                        } else {
                            $offer = Offer::factory()->create([
                                'user_id' => $designer->id,
                                'project_id' => $inProgressProject->id,
                                'type' => OfferType::Final,
                                'status' => OfferStatus::Accepted
                            ]);

                            Milestone::factory()->create([
                                'offer_id' => $offer->id,
                                'status' => MilestoneStatus::Pending,
                                'price' => $offer->price / 2,
                            ]);
                            Milestone::factory()->create([
                                'offer_id' => $offer->id,
                                'status' => MilestoneStatus::Waiting,
                                'price' => $offer->price / 2,
                            ]);
                        }
                    }
                }

                $pendingProjects = Project::factory(rand(0, 3))->create([
                    'client_id' => $currentUser->id,
                    'status' => Status::Pending,
                    'published_at' => null
                ]);

                foreach ($pendingProjects as $pendingProject) {
                    $this->projectApprovalTicket($currentUser, $pendingProject);
                }
            }
        }
    }
}
