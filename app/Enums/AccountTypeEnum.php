<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class AccountTypeEnum extends Enum
{
    const global = 'Herkes Acik';
    const personal = 'Musteriye Ozel';
    const all = 'Karisik Kullanim';

    public static function get($key): string
    {
        if ($key === 'global') {
            return self::global;
        } else if ($key === 'personal') {
            return self::personal;
        } else if ($key === 'all') {
            return self::all;
        }

        return '';
    }
}
