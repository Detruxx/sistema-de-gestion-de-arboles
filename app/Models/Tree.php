<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    protected $table = 'trees';

    protected $primaryKey = 'id';
    protected $fillable = [
        //Claves Foraneas
        'species_id', // Clave foranea de Species
        'planter_id', // Clave foranea de Plantera
        'street_id', // Clave foranea de Street
        'park_id', // Clave foranea de Park 

        // Datos geograficos
        'latitude', // latitud
        'longitude', // longitud

        'height', // altura
        'dap', // diametro a la altura del pecho

        //Datos secundarios
        'reference', // Referencia segun la chapa de la calle
        'maintenance_status', // si hay un reclamo pendiente, o no
        'vitality', // vital, en mal estado, muerto
        'structure', //Estructura del arbol
        'degree', // degree del arbol
        'observations', // observaciones, datos varios
    ];

    // RELACIONES

    // Relacion con Species
    public function species()
    {
        return $this->belongsTo(Species::class, 'species_id');
    }

    // Relacion con Plantera
    public function plantera()
    {
        return $this->belongsTo(Plantera::class, 'planter_id');
    }
    
    // Relacion con Street
    public function street()
    {
        return $this->belongsTo(Street::class, 'street_id');
    }

    // Relacion con Park
    public function park()
    {
        return $this->belongsTo(Park::class, 'park_id');
    }
}
