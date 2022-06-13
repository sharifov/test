<?php

namespace src\model\phoneList\services;

use src\model\phoneList\entity\PhoneList;

class InternalPhones
{
    public static function isExist(string $number): bool
    {
        return PhoneList::find()->byPhone($number)->exists();
    }
}
