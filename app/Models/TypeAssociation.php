<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class TypeAssociation extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
    protected $table = 'type_associations';

    protected $fillable = ['id'];
    public $translatedAttributes = [ 'name'];

    

}
