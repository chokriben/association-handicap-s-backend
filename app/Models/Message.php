<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id_expediteur',
        'utilisateur_id_destinataire',
        'date_envoi',
    ];

    public function expéditeur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id_expediteur');
    }

    public function destinataire()
    {
        return $this->belongsTo(User::class, 'utilisateur_id_destinataire');
    }

    public function translations()
    {
        return $this->hasMany(MessageTranslation::class);
    }
}

