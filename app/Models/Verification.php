<?php

namespace App\Models;

use App\Enum\VerificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    /** @use HasFactory<\Database\Factories\VerificationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'attachment',
        'verified',
    ];

    protected $casts = [
        'type' => VerificationType::class,
        'attachment' => 'array',
        'verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
