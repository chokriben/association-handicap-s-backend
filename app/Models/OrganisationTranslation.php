<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'adresse',
        'adresse_reception',
        'description',
        'category'
    ];

    
}
