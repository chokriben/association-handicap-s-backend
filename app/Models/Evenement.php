<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;


class Evenement extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;
    protected $table = 'evenements';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'event_date',
        'location',
        'capacity',
        'contact_email',
    ];
    public $translatedAttributes = ['title', 'description','location'];


    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

}
