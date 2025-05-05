<?php

namespace Database\Seeders\Build;

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
        Category::create([
            'name' => 'Project Approval',
            'slug' => 'project-approval',
            'is_visible' => true,
        ]);

        Category::create([
            'name' => "User ID",
            'slug' => "user-id",
            'is_visible' => true,
        ]);

        Category::create([
            'name' => "Company ID",
            'slug' => "company-id",
            'is_visible' => true,
        ]);

        Category::create([
            'name' => "Address Verification",
            'slug' => "address-verification",
            'is_visible' => true,
        ]);

        Category::create([
            'name' => "Restore Account",
            'slug' => "restore-account",
            'is_visible' => true,
        ]);
    }
}
