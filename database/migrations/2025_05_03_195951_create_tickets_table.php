<?php

namespace Coderflex\LaravelTicket\Database\Factories;

use Coderflex\LaravelTicket\Models\Category;
use Coderflex\LaravelTicket\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id');
            $table->foreignIdFor(Category::class)->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->bigInteger('model_id', false, true)->nullable();
            $table->string('priority')->default('low');
            $table->string('status')->default('open');
            $table->boolean('is_resolved')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
