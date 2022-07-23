<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaparaDeposit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'bank_info' => 'array',
        'created_at' => 'date:Y-m-d h:i:s',
        'updated_at' => 'date:Y-m-d h:i:s'
    ];

    public function transaction(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PaparaAccount::class, 'bank_account_id');
    }
}
