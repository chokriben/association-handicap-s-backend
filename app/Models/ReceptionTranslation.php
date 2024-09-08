<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ReceptionTranslation extends Model
{
    public $fillable = ['nom', 'prenom','adresse','message'];
    public $timestamps = false;
}
