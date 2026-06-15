<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Street extends Model
{
    protected $table = 'streets';
    protected $primaryKey = 'id';
    protected $fillable = [
        'street_name', // Nombre de la calle
        'street_number', // Numero de la calle
        'district', // Comuna / distrito
        'door_plate', // Chapa física / nro de puerta
    ];

    // RELACIONES
    // Relacion con Tree
    public function trees()
    {
        return $this->hasMany(Tree::class, 'street_id');
    }

    // Relacion con Planter
    public function planters()
    {
        return $this->hasMany(Planter::class, 'street_id');
    }

    use HasFactory;

}