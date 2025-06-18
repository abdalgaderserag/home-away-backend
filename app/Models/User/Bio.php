<?php

namespace App\Models\User;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bio extends Model
{
    /** @use HasFactory<\Database\Factories\User\BioFactory> */
    use HasFactory;

    protected $fillable = [
        'about',
        'price_per_meter',
        'location_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location() : BelongsTo {
        return $this->belongsTo(Location::class);
    }
}
