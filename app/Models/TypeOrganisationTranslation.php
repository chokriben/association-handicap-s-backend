<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeOrganisationTranslation extends Model
{

     // Définir le nom de la table
    protected $table = 'type_organisations_translations';

    protected $fillable = [
        'name',
    ];

}
