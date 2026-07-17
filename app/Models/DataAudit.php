<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataAudit extends Model
{
    protected $fillable = ['tree_id', 'conflict_type', 'description', 'resolved'];
}
