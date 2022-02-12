<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HavaleWithdraw extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'date:Y-m-d h:i:s',
        'updated_at' => 'date:Y-m-d h:i:s'
    ];

    public function transaction(): \Illuminate\Database\Eloquent\Relations\morphOne
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }
}
