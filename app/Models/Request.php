<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'user_id',
        'tree_id',
        'request_type_id',
        'street_id',
        'description',
        'status',
    ];

    // Relación con las fotos del reclamo
    public function photos()
    {
        return $this->hasMany(Photo_Tree::class, 'request_id');
    }
}
