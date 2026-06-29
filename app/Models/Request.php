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

    /**
     * Obtener todo el historial de cambios de estado para este reclamo.
     */
    public function histories()
    {
        return $this->hasMany(RequestStatusHistory::class)->orderBy('created_at', 'asc');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tree()
    {
        return $this->belongsTo(Tree::class);
    }

    public function requestType()
    {
        return $this->belongsTo(RequestType::class, 'request_type_id');
    }

    public function street()
    {
        return $this->belongsTo(Street::class);
    }

    /**
    * Vinculamos las Ordenes de Trabajo al reclamo
    * Obtener las órdenes de trabajo/empresas asignadas a este reclamo.
    */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
