<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'date_creation',
        'utilisateur_id',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class);
    }

    public function publications()
    {
        return $this->hasMany(Publication::class);
    }

    public function translations()
    {
        return $this->hasMany(OrganisationTranslation::class);
    }
}
