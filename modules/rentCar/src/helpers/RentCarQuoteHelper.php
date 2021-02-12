<?php

namespace modules\rentCar\src\helpers;

use modules\rentCar\src\entity\rentCar\RentCar;

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
    public static function nameGenerator(RentCar $rentCar, array $data, string $separator = ' '): string
    {
        return $rentCar->prc_pick_up_code . $separator .
            $rentCar->prc_pick_up_date . $separator .
            RentCarDataParser::getModelName($data);
    }
}
