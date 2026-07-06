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
        'description', 'path', 'request_status_id',
        'linked_to', 'suggested_duplicate_id'
    ];

    /**
     * Configuramos el casteo automático de la columna path
     */
    protected $casts = [
        'path' => 'array', // Laravel transforma el JSON de la BDD a array de PHP solo
    ];
    
    // Esto es para que se pueda acceder al codigo de seguimiento como si fuera una propiedad normal
    
    protected $appends = ['tracking_code'];

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

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    /**
    * Vinculamos las Ordenes de Trabajo al reclamo
    * Obtener las órdenes de trabajo/empresas asignadas a este reclamo.
    */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function linkedRequest()
    {
        return $this->belongsTo(Request::class, 'linked_to');
    }

    public function suggestedDuplicate()
    {
        return $this->belongsTo(Request::class, 'suggested_duplicate_id');
    }

    /**
     * Obtiene el código formateado de seguimiento.
     * Se accede como $request->tracking_code
     */
    public function getTrackingCodeAttribute()
    {
        $year = $this->created_at ? $this->created_at->format('Y') : date('Y');
        return 'REC-' . $year . '-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}
