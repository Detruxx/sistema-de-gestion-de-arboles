<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestStatus extends Model
{
    use HasFactory;

    // Le avisamos a Laravel qué campos se pueden llenar de forma masiva
    protected $fillable = ['status_name', 'slug', 'sequence', 'is_terminal', 'color'];

    /**
     * Relación inversa: Un estado pertenece a muchos reclamos
     */
    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
