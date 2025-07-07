<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingReport extends Model
{
    protected $table = 'billing_report';

    protected $fillable = ['user_id', 'keterangan', 'qty', 'bill_amount',  'is_sent'];

    public function user()
    {
        return $this->belongsTo(user::class);
    }
}
