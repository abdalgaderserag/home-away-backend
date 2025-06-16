<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'model_id',
        'description',
        'priority',
        'status',
        'assigned_to',
        'is_resolved',
        'is_locked',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'model_id', 'id');
    }

    public function verification()
    {
        return $this->belongsTo(verification::class, 'model_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assigned(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }
}
