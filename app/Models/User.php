<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = ['user', 'email', 'password', 'role', 'id_parent'];

    protected $hidden = ['password'];

    public function parent()
    {
        return $this->belongsTo(User::class, 'id_parent');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'id_parent');
    }

    public function routers() {
        return $this->hasMany(Router::class);
    }

    public function paketVouchers() {
        return $this->hasMany(Router::class);
    }

    public function userLists() {
        return $this->hasMany(Router::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
