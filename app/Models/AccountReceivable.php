<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountReceivable extends Model
{
    use HasFactory;

    protected $fillable = [
        'datetime',
        'third_id',
        'concept',
        'charge',
        'deposit',
        'days_deadline',
        'balance',
    ];

    public function third () {
        return $this->belongsTo(Third::class);
    }
}
