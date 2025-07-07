<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
    protected $table = 'user_list';

    protected $fillable = [
        'server',
        'user',
        'user_password',
        'profile',
        'uptime',
        'used_at',
        'is_used',
        'is_active',
        'time_limit',
        'data_limit',
        'comment',
        'paket_voucher_id',
        'user_id',
    ];

    /**
     * Relasi ke user Laravel (penjual/admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paketVouchers()
    {
        return $this->belongsTo(PaketVoucher::class, 'paket_voucher_id');
    }
}
