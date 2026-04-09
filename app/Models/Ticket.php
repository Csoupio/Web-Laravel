<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'projet_id',
        'statut',
        'priorite',
        'type',
        'temps_estime',
        'mode_facturation',
        'validation_client',
        'commentaire_refus',
        'facturable_auto',
    ];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }
}
