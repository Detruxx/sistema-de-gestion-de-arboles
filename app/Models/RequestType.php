<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    use HasFactory;

    protected $table = 'request_types';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'task_description', 

    ];

    // Relacion con Request
    public function requests()
    {
        return $this->hasMany(Request::class, 'request_type_id');
    }
}
