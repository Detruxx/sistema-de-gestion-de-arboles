<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'role',
        'company_id',
        'profile_photo',
        'user_status_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_status_id' => 'integer', // Fuerza a entero siempre
        ];
    }

    // Un usuario puede pertenecer a una empresa contratista
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación: Un usuario tiene un único estado asignado
     */
    public function status()
    {
        //Apunta a UserStatus mediante 'user_status_id'
        return $this->belongsTo(UserStatus::class, 'user_status_id');
    }
}
