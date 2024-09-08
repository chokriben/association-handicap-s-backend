<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Reception extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $table = 'receptions';

    protected $fillable = [
        'email', // Modification ici
        'num_postale',
    ];

    public $translatedAttributes = ['message,nom,prenom,adresse'];
}
