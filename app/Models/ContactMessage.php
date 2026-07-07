<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';

    protected $fillable = [
        'user_id',
        'message',
        'status',
        'inspector_response', 
        'is_new_for_user',
    ];

    /**
     * Relacion con el usuario que envio el mensaje
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
