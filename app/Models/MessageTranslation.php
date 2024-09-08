<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'locale',
        'message_id',
        'contenu',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}

