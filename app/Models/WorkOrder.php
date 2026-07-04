<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkOrder extends Model
{
    use HasFactory; 

    protected $table = 'work_orders';

    protected $fillable = [
        'request_id', 
        'company_id', 
        'task_description', 
        'scheduled_date', 
        'execution_order',
        'work_status'
    ];

    // Indicarle a Laravel que este campo es una fecha para poder usar ->format() en Blade
    protected $casts = [
        'scheduled_date' => 'date',
    ];

    // Cada orden pertenece a un único reclamo
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    // Cada orden está asignada a una única empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
