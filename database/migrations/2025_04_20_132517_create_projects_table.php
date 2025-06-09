<?php

use App\Models\Location;
use App\Models\Skill;
use App\Models\UnitType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users');
            $table->integer('designer_id', 0, 1)->nullable();
            $table->foreignIdFor(Location::class)->constrained('locations');
            $table->foreignIdFor(Skill::class)->constrained('skills');
            $table->foreignIdFor(UnitType::class)->constrained('unit_types');
            // $table->string('location')->nullable();
            // $table->string('skill')->nullable();
            // $table->enum('unit_type', ['house', 'apartment', 'villa'])->nullable();
            $table->enum('status', ['draft', 'pending', 'published', 'in_progress', 'completed'])->default('draft');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('space')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->float('min_price')->nullable();
            $table->float('max_price')->nullable();
            $table->boolean('resources')->default(false);
            // $table->enum('skill', ['construction', 'design', 'renovation'])->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
