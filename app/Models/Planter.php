<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planter extends Model
{
    use HasFactory;

    protected $table = 'planters';
    protected $primaryKey = 'id';

    protected $fillable = [
        'street_id', //Clave foranea de Street

        'planter_state', // Estado de ocupacion de la plantera
        'position', // Posicion de la plantera
        'height', // Altura de la plantera
        'street_width', // Ancho de la vereda 
    ];

    // RELACIONES

    // Relacion con Street
    public function street()
    {
        return $this->belongsTo(Street::class, 'street_id');
    }
}
