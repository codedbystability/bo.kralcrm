<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TransactionStatusEnum extends Enum
{
    const waiting = 'Beklemede';
    const approved = 'Banka Gonderildi';
    const cancelled = 'Iptal Edildi';
    const completed = 'Tamamlandi';

    public static function get($key): string
    {
        if ($key === 'waiting') {
            return self::waiting;
        } else if ($key === 'approved') {
            return self::approved;
        } else if ($key === 'cancelled') {
            return self::cancelled;
        } else if ($key === 'completed') {
            return self::completed;
        }

        return '';
    }
}
