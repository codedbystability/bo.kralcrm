<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = [
        'todayCount', 'todayDeposit', 'todayWithdraw', 'todayNet'
    ];

    public function accountable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'type_id');
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'client_id');
    }

    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function getTodayCountAttribute(): int
    {
        return $this->transactions()->whereDate('updated_at', Carbon::today())->count();
    }

    public function getTodayDepositAttribute(): int
    {
        return $this->transactions()->whereDate('updated_at', Carbon::today())
            ->whereHas('type', function ($query) {
                return $query->where('key', 'deposit');
            })
            ->sum('amount');
    }


    public function getTodayWithdrawAttribute(): int
    {
        return $this->transactions()->whereDate('updated_at', Carbon::today())
            ->whereHas('type', function ($query) {
                return $query->where('key', 'withdraw');
            })
            ->sum('amount');
    }

    public function getTodayNetAttribute(): int
    {
        return floatval($this->todayDeposit - $this->todayWithdraw);
    }
}
