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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('project_id')->constrained('projects');
            $table->enum('status', ['accepted', 'declined', 'pending', 'completed']);
            $table->float('price',20, 2);
            $table->timestamp('deadline')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->text('description');
            $table->enum('type', ['basic', 'final']);
            $table->timestamp('expire_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
