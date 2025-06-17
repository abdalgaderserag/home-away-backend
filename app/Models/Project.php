<?php

namespace App\Models;

use App\Enum\Project\Status;
use App\Models\Location;
use App\Models\UnitType;
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
        'deadline' => 'datetime',
        'attachment' => 'array',
    ];

    protected $fillable = [
        'client_id',
        'designer_id',
        'status',
        'title',
        'description',
        'unit_type_id',
        'space',
        'location_id',
        'deadline',
        'min_price',
        'max_price',
        'resources',
        'skill_id',
        'published_at'
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

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function unit()
    {
        return $this->belongsTo(UnitType::class, 'unit_type_id', 'id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }
}
