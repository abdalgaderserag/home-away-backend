<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    /** @use HasFactory<\Database\Factories\AttachmentFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function verification()
    {
        return $this->belongsTo(Verification::class);
    }
}
