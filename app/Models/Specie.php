<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specie extends Model
{
    protected $table = 'species';
    protected $primaryKey = 'id';
    protected $fillable = [
        'scientific_name', // Nombre de la especie
        'common_name', // Nombre comun de la especie
        'family', // Familia de la especie
        'origin', // Origen de la especie
        'foliage_type', // Tipo de follaje de la especie
    ];

    // RELACIONES
    // Relacion con Tree
    public function trees()
    {
        return $this->hasMany(Tree::class, 'species_id');
    }

    use HasFactory;
}