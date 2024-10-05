<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberTranslation extends Model
{
  

    // Update to match your table name
    protected $table = 'member_translations';

    // Specify the attributes that are mass assignable
    protected $fillable = ['name', 'prenom', 'adresse'];
}
