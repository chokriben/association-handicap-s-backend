<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Association  extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
    protected $table = 'associations';

    protected $fillable = [
    'phone',
    'phone_fax',
    'rip',
    'email'];
    public $translatedAttributes = [ 'name','description','adresse_reception','adresse'];

 // Définir la relation avec le type d'association
 public function typeAssociation()
 {
     return $this->belongsTo(TypeAssociation::class);
 }
 public function admin()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
