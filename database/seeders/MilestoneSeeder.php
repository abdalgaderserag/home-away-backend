<?php
namespace Database\Seeders;

use App\Models\Milestone;
use App\Models\Offer;
use Illuminate\Database\Seeder;

class MilestoneSeeder extends Seeder
{
    public function run()
    {
        Offer::all()->each(function ($offer) {
            Milestone::factory()->count(3)->create([
                'offer_id' => $offer->id
            ]);
        });
    }
}