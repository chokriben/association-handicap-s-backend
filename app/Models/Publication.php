<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Publication extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
    protected $table = 'publications';
    protected $fillable = [
        'date_publication',
        'active',
    ];
    public $translatedAttributes = ['titre', 'contenu'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

