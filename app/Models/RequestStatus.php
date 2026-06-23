<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestStatus extends Model
{
    use HasFactory;

    // Le avisamos a Laravel qué campos se pueden llenar de forma masiva
    protected $fillable = ['status_name', 'slug'];

    /**
     * Relación inversa: Un estado pertenece a muchos reclamos
     */
    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
