<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['company_name'];

    // Una empresa puede tener muchas órdenes de trabajo asignadas
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
