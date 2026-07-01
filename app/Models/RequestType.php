<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Relations\HasMany;
>>>>>>> b4619f85ce7365f09993321fe82f406954597893

class RequestType extends Model
{
    use HasFactory;

    protected $table = 'request_types';
<<<<<<< HEAD
}
=======

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
>>>>>>> b4619f85ce7365f09993321fe82f406954597893
