<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'projet_user', 'user_id', 'projet_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'user_id');
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class, 'user_id', 'id');
    }
}

