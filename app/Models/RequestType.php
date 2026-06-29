<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestType extends Model
{
    use HasFactory;

    protected $table = 'request_types';

    protected $fillable = [
        'task_description',
    ];

    /**
     * Obtener todos los reclamos asociados a este tipo de incidencia.
     */
    public function requests()
    {
        return $this->hasMany(Request::class, 'request_type_id');
    }
}