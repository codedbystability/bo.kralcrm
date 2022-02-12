<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TransactionTypeEnum extends Enum
{
    const deposit = 'Yatirim';
    const withdraw = 'Cekim';

    public static function get($key): string
    {
        if ($key === 'deposit') {
            return self::deposit;
        }
        return self::withdraw;
    }
}
