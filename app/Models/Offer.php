<?php

namespace App\Models;

use App\Enum\Offer\OfferStatus;
use App\Enum\Offer\OfferType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    /** @use HasFactory<\Database\Factories\OfferFactory> */
    use HasFactory;

    protected $casts = [
        'type' => OfferType::class,
        'status' => OfferStatus::class,
        'deadline' => 'datetime',
        'start_date' => 'datetime',
        'expire_date' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'project_id',
        'price',
        'deadline',
        'start_date',
        'description',
        'type',
        'expire_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function milestones() {
        return $this->hasMany(Milestone::class);
    }
}
