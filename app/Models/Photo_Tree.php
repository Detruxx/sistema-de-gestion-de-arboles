<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo_Tree extends Model
{
    use HasFactory;

    protected $table = 'photo__trees';

    protected $fillable = [
        'photo_path',
        'type',
        'tree_id',
        'request_id',
    ];

    // Relación con el Árbol
    public function tree()
    {
        return $this->belongsTo(Tree::class, 'tree_id');
    }

    // Relación con el Reclamo/Solicitud
    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
}
