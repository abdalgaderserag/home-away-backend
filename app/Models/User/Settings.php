<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settings extends Model
{
    /** @use HasFactory<\Database\Factories\User\SettingsFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lang',
        'mail_notifications',
        'sms_notifications'
    ];

    protected $casts = [
        'mail_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
