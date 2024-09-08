<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssociationPresentationTranslation extends Model
{
    protected $fillable = [
        'de_nous',
        'notre_vision',
        'notre_message',
        'nos_objectifs',
        'de_nouvelles_valeurs'
    ];

    
}
