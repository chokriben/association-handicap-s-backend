<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'plain_password',
        'nom_association',
        'type_organisation',
        'adresse',
        'telephone',
        'role',
        'status',
        'profile_photo',
    ];
    public function organisations()
    {
        return $this->hasMany(Organisation::class);
    }

    public function publications()
    {
        return $this->hasMany(Publication::class);
    }

    public function messagesEnvoyés()
    {
        return $this->hasMany(Message::class, 'utilisateur_id_expediteur');
    }

    public function messagesReçus()
    {
        return $this->hasMany(Message::class, 'utilisateur_id_destinataire');
    }
    public function members()
    {
        return $this->hasMany(User::class, 'admin_id');
    }
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
