<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssociationTranslation extends Model
{
    protected $table = 'association_translations';

    protected $fillable = [
        'name',
        'adresse',
        'adresse_reception',
        'description',
    ];

   // public $timestamps = false;
}
