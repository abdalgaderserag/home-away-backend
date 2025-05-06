<?php

namespace App\Models;

use App\Enum\Offer\MilestoneStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Milestone extends Model
{
    /** @use HasFactory<\Database\Factories\MilestoneFactory> */
    use HasFactory;
    protected $casts = [
        'status' => MilestoneStatus::class,
        'deadline' => 'datetime',
        'delivery_date' => 'datetime',
    ];

    protected $fillable = [
        'offer_id',
        'status',
        'deadline',
        'delivery_date',
        'price',
        'description'
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function project(): HasOneThrough
    {
        return $this->hasOneThrough(
            Project::class,
            Offer::class,
            'id',
            'id',
            'offer_id',
            'project_id'
        );
    }

    public function attachment(): HasOne
    {
        return $this->hasOne(Attachment::class);
    }
}
