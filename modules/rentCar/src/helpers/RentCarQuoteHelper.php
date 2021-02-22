<?php

namespace modules\rentCar\src\helpers;

use modules\rentCar\src\entity\rentCar\RentCar;
use yii\helpers\StringHelper;

/**
 * Class RentCarQuoteHelper
 */
class RentCarQuoteHelper
{
    /**
     * @param RentCar $rentCar
     * @param array $data
     * @param string $separator
     * @return string
     */
    public static function nameGenerator(RentCar $rentCar, array $data, string $separator = ' | '): string
    {
        $name = $rentCar->prc_pick_up_code  . $separator . RentCarDataParser::getModelName($data);
        return StringHelper::truncate($name, 37);
    }
}
