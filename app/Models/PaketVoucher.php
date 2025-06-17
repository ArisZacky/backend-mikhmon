<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketVoucher extends Model
{
    use HasFactory;

    protected $table = 'paket_voucher';

    protected $fillable = [
        'voucher_name',
        'address_pool',
        'shared_user',
        'rate_limit',
        'expired_mode',
        'price',
        'selling_price',
        'lock_user',
        'parent_queue',
        'router_id',
        'user_id',
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
