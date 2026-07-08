<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    use HasFactory;

    protected $table = 'user_statuses'; // Le avisamos a Laravel el nombre exacto de la tabla

    protected $fillable = ['name', 'slug'];
}