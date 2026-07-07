<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    protected $fillable = ['priority_name', 'slug',];

    // Una prioridad puede pertenecer a muchos reclamos
    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
