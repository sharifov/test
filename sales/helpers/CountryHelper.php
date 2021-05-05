<?php

namespace sales\helpers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class CountryHelper
 */
class CountryHelper
{
    public const ADDITIONAL_COUNTRIES = [
        [
            'id'        => 1001,
            'name'      => 'Taiwan',
            'alpha2'    => 'tw',
            'alpha3'    => 'tai',
        ],
    ];

    /**
     * @param string $lang
     * @param bool $additional
     * @return array
     */
    public static function getCountries(string $lang = 'en', bool $additional = true): array
    {
        $result = [];
        $fileName = Yii::getAlias('@root/vendor') . '/stefangabos/world_countries/data/' . $lang . '/countries.php';

        if (!file_exists($fileName)) {
            throw new \RuntimeException('Error file (' . $fileName . ') not exist');
        }

        require $fileName;
        $countries = $countries ?? [];
        if (is_array($countries)) {
            $result = $countries;
        }
        if ($additional) {
            $result = ArrayHelper::merge($result, self::ADDITIONAL_COUNTRIES);
            ArrayHelper::multisort($result, ['alpha2'], [SORT_ASC]);
        }

        return $result;
    }
}
