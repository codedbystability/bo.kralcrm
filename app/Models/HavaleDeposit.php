<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HavaleDeposit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'bank_info' => 'array',
        'created_at' => 'date:Y-m-d h:i:s',
        'updated_at' => 'date:Y-m-d h:i:s'
    ];

    public function transaction(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
}
