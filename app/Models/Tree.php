<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    //

    protected $casts = [
        'vitality' => 'array', // Cada vez que guarda/lea este campo, lo conviérte automáticamente en un Array de PHP
    ];
}
