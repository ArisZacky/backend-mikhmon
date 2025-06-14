<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Reseller extends Authenticatable implements JWTSubject
{
    protected $fillable = ['nama_reseller', 'email', 'pass'];
    protected $hidden = ['pass'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
