<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = ['agent_id', 'jumlah', 'total_price', 'is_sent_to_contract'];
    protected $casts = [
        'total_price' => 'float',
        'is_sent_to_contract' => 'boolean',
    ];



    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}
