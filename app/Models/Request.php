<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    // Asegúrate de tener 'request_status_id' en tu array $fillable si lo usás
    protected $fillable = [
        'user_id', 'tree_id', 'request_type_id', 'street_id', 
        'description', 'path', 'request_status_id'
    ];

    /**
     * Relación: Un reclamo tiene UN estado asignado
     */
    public function status()
    {
        // Laravel buscará automáticamente la columna 'request_status_id' por convención
        return $this->belongsTo(RequestStatus::class, 'request_status_id');
    }
}
