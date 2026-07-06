<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // 📍 Importante para poder usar Company::factory() si lo necesitas
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // Actualizamos los campos permitidos para asignación masiva (Mass Assignment)
    // Cambiamos 'company_name' por 'name' (o el nombre exacto que pusiste en tu migración)
    protected $fillable = [
        'name',
        'business_name',
        'cuit',
        'email',
        'location',
    ];

    /**
     *Una empresa tiene muchos roles/trabajos asignados
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
}
