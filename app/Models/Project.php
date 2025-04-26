<?php

namespace App\Models;

use App\Enum\Project\Status;
use App\Enum\Project\UnitType;
use App\Enum\Project\Location;
use App\Enum\Project\Skill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $casts = [
        'status' => Status::class,
        'unit_type' => UnitType::class,
        'location' => Location::class,
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

    public function client()
    {
        return $this->belongsTo(User::class);
    }

    public function designer()
    {
        return $this->belongsTo(User::class);
    }
}
