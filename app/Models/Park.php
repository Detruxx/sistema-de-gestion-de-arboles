<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Park extends Model
{
    protected $table = 'parks';
    protected $primaryKey = 'id';

    protected $fillable = [
        'park_name', // Nombre del parque
        'district', // Comuna / distrito
         
        'latitude', // Latitud del parque
        'longitude', // Longitud del parque
    ];

    // Relacion con Arboles
    public function trees()
    {
        return $this->hasMany(Tree::class, 'park_id');
    }
}
