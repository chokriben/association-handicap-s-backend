<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeAssociationTranslation extends Model
{
    protected $table = 'type_associations_translations';

    protected $fillable = [
        'name',
    ];

   // public $timestamps = false;
}
