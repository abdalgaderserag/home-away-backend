<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    /** @use HasFactory<\Database\Factories\ChatFactory> */
    use HasFactory;
    protected $fillable = [
        'project_id',
        'first_user',
        'second_user',
        'last_message_id',
        'attachments',
        'is_read',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function firstUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'first_user');
    }
    public function secondUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'second_user');
    }
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }
}
