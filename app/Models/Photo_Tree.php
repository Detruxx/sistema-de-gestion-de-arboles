<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo_Tree extends Model
{
    protected $table = 'photo_trees';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tree_id',
        'photo_path',
    ];

    // RELACIONES
    // Relacion con Tree
    public function tree()
    {
        return $this->belongsTo(Tree::class, 'tree_id');
    }
    use HasFactory;
}
