<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    // RELACIONES
    // Relacion con Tree
    public function trees()
    {
        return $this->hasMany(Tree::class, 'park_id');
    }
    
    use HasFactory;
}
