<?php

namespace App\Models;

use Coderflex\LaravelTicket\Models\Category;
use Coderflex\LaravelTicket\Models\Ticket as ModelsTicket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ticket extends ModelsTicket
{
    public function category(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'message', 'id');
    }
}
