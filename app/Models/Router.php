<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    protected $table = 'router';

    protected $fillable = [
        'session_name',
        'ip_mikrotik',
        'user_mikrotik',
        'password_mikrotik',
        'hostpot_name',
        'dns_name',
        'currency',
        'auto_reload',
        'idle_timeout',
        'traffic_interface',
        'live_report',
        'user_id',
    ];

    /**
     * Relasi ke model User (pemilik router)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke paket voucher, 1 router bisa punya banyak paket
    public function paketVouchers()
    {
        return $this->hasMany(PaketVoucher::class);
    }
}
