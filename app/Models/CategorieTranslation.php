<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'categorie_id',
        'nom',
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }
}
