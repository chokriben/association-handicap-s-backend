<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class AssociationPresentation extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
    protected $table = 'association_presentations';

    protected $fillable = ['id'];
    public $translatedAttributes = [ 'de_nous',
    'notre_vision',
    'notre_message',
    'nos_objectifs',
    'de_nouvelles_valeurs'];



}
