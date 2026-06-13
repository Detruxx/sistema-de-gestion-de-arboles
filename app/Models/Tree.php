<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    use HasFactory;

    protected $table = 'trees';

    protected $primaryKey = 'id';

    protected $casts = [
        'vitality' => 'array', // Cada vez que guarda/lea este campo, lo convierte automáticamente en un Array de PHP
    ];

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

    // Relacion con Specie
    public function specie()
    {
        return $this->belongsTo(Specie::class, 'species_id');
    }

    // Relacion con Plantera
    public function planter()
    {
        return $this->belongsTo(Planter::class, 'planter_id');
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

    // Relación con sus fotos
    public function photos()
    {
        return $this->hasMany(Photo_Tree::class, 'tree_id');
    }

    // Relación con fotos oficiales únicamente (DNI)
    public function officialPhotos()
    {
        return $this->hasMany(Photo_Tree::class, 'tree_id')->where('type', 'official');
    }
}
