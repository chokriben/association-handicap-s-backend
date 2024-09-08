<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        // Ajoutez des champs si nécessaire pour la table principale
    ];

    public function translations()
    {
        return $this->hasMany(CategorieTranslation::class);
    }

    public function publications()
    {
        return $this->hasMany(Publication::class);
    }
}

