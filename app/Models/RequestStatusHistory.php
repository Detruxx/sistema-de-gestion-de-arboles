<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['request_id', 'request_status_id', 'user_id', 'justification'];

    // Relación: Un registro de historial pertenece a un reclamo
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    // Relación: Nos dice qué estado se asignó en este movimiento
    public function status()
    {
        return $this->belongsTo(RequestStatus::class, 'request_status_id');
    }

    // Relación: Nos dice qué usuario/inspector redactó este cambio
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
