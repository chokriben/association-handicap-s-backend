<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Organisation extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
    protected $fillable = [
        'phone',
        'phone_fax',
        'rip',
        'email'
    ];
    public $translatedAttributes = ['name', 'description', 'category', 'adresse_reception', 'adresse'];

    // Définir la relation avec le type d'association
    public function typeOrganisation()
    {
        return $this->belongsTo(TypeOrganisation::class);
    }
    public function admin()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
