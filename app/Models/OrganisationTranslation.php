<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'organisation_id',
        'nom',
        'description',
        'adresse_reception',
        'adresse_locale'
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}
