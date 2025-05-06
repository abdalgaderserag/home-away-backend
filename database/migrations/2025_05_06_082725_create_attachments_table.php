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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('project_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('message_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('milestone_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('verification_id')->constrained()->onDelete('cascade')->nullable();
            $table->text("url");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
