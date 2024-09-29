<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Member extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $table = 'members';

    protected $fillable = [
        'email',
        'phone',
        'password'
    ];

    // Corrected translatedAttributes to be an array
    public $translatedAttributes = ['name', 'prenom', 'adresse'];

    protected $hidden = [
        'password',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
