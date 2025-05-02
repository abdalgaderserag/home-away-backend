<?php

namespace App\Models;

use App\Enum\Project\Status;
use App\Enum\Project\UnitType;
use App\Enum\Project\Location;
use App\Enum\Project\Skill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $casts = [
        'status' => Status::class,
        'unit_type' => UnitType::class,
        'skill' => Skill::class,
        'deadline' => 'datetime',
        'attachments' => 'array',
    ];

    protected $fillable = [
        'client_id',
        'designer_id',
        'status',
        'title',
        'description',
        'unit_type',
        'space',
        'location',
        'deadline',
        'min_price',
        'max_price',
        'resources',
        'skill',
        'attachments'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }

    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_id', 'id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }
}
