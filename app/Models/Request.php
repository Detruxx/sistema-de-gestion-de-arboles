<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request extends Model
{
    // POR AHORA, la tabla estaria asi

    protected $table = 'requests';
    protected $primaryKey = 'id';

    protected $fillable = [

        'tree_id', //Clave foranea de tree
        'user_id', //Clave foranea de user
        'request_type_id', //Clave foranea de request_type

        // Datos secundarios
        'description', // Descripcion del reclamo
        'status', // Estado del reclamo (pendiente, en proceso, resuelto)
    ];

   // RELACIONES 

    // Relacion con Tree
    public function tree()
    {
        return $this->belongsTo(Tree::class, 'tree_id');
    }

    // Relacion con Street
    public function street()
    {
        return $this->belongsTo(Street::class, 'street_id');
    }

    // Relacion con User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relacion con Request_Type
    public function Request_Type() 
    {
        return $this->belongsTo(RequestType::class, 'request_type_id');
    } 

    use HasFactory;
}