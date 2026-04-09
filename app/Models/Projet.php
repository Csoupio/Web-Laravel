<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    protected $fillable = [
        'nom',
        'client_id',
        'description',
        'contrat_heures',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function collaborateurs()
    {
        return $this->belongsToMany(User::class, 'projet_user');
    }
}
