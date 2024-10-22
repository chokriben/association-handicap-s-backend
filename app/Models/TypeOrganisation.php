<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class TypeOrganisation extends  Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
     // Définir le nom de la table
    protected $table = 'type_organisations';

    protected $fillable = ['id'];
    public $translatedAttributes = [ 'name'];

}
