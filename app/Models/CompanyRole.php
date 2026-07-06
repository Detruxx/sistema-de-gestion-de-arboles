<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyRole extends Model
{
    use HasFactory;

    protected $table = 'company_roles'; // Aseguramos el nombre de la tabla

    protected $fillable = [
        'company_id',
        'job_role',
    ];

    /**
     * Relación inversa: Un rol pertenece a una Empresa.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
