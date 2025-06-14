<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotspotUser extends Model
{
    //
    protected $fillable = [
        'server', 'user', 'address', 'mac', 'uptime', 
        'bytes_in', 'bytes_out', 'time_left', 'login_by', 'comment'
    ];
    
}
