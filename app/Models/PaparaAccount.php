<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaparaAccount extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\morphOne
    {
        return $this->morphOne(Account::class, 'accountable');
    }

    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

}
