<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // Actualizamos los campos permitidos para asignación masiva (Mass Assignment)
    protected $fillable = [
        'name',
        'business_name',
        'cuit',
        'email',
        'location',
        'user_status_id', 
    ];

    protected $casts = [
        'user_status_id' => 'integer', // Fuerza a entero siempre
    ];

    /**
     * Una empresa tiene muchos roles/trabajos asignados
     */
    public function jobRoles()
    {
        return $this->hasMany(CompanyRole::class);
    }

    /**
     * Una empresa puede tener muchas órdenes de trabajo asignadas
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Relación: Una empresa tiene un único estado asignado
     */
    public function status()
    {
        //Reutiliza el modelo UserStatus
        return $this->belongsTo(UserStatus::class, 'user_status_id');
    }
}
