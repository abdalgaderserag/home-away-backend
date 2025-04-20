<?php

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
            $table->foreignId('designer_id')->nullable()->constrained('users');
            $table->enum('status', ['draft', 'pending', 'published', 'in_progress', 'completed'])->default('draft');
            $table->string('title');
            $table->text('description');
            $table->enum('unit_type', ['house', 'apartment', 'villa']);
            $table->integer('space');
            $table->enum('location', ['cairo']);
            $table->timestamp('deadline');
            $table->float('min_price');
            $table->float('max_price');
            $table->boolean('resources')->default(false);
            $table->enum('skill', ['construction', 'design', 'renovation']);
            $table->json('attachments')->nullable();
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
