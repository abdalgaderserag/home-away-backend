<?php

namespace Database\Seeders;

use Coderflex\LaravelTicket\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->make([
            'name' => 'Project Approval',
            'description' => 'Project Approval',
            'slug' => 'project-approval',
        ])->save();

        Category::factory()->make([
            'name' => "User ID",
            'description' => "User ID verification request",
            'slug' => "user-id"
        ])->save();

        Category::factory()->make([
            'name' => "Company ID",
            'description' => "Company ID verification request",
            'slug' => "company-id"
        ])->save();

        Category::factory()->make([
            'name' => "Address Verification",
            'description' => "User Address verification request",
            'slug' => "address-verification",
        ])->save();

        Category::factory()->make([
            'name' => "Restore Account",
            'description' => "Restore account request send a reset password link if approved.",
            'slug' => "restore-account",
        ])->save();
    }
}
