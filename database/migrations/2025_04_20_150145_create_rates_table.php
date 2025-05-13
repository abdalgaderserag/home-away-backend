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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained();
            $table->enum('type', ['client', 'designer']);
            $table->foreignId('client_id')->nullable()->constrained('users');
            $table->foreignId('designer_id')->nullable()->constrained('users');
            $table->unsignedTinyInteger('rate')->default(1);
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
