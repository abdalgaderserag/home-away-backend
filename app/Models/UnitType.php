<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    /** @use HasFactory<\Database\Factories\UnitTypeFactory> */
    use HasFactory;

    protected $fillable = ["type"];
}
