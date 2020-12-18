<?php

namespace sales\helpers\projectLocale;

use yii\helpers\ArrayHelper;

/**
 * Class ProjectLocaleHelper
 */
class ProjectLocaleHelper
{
    public static function getSelectedLocale(array $localeList, ?string $clientLocale, ?string $projectDefault): string
    {
        if ($clientLocale && ArrayHelper::keyExists($clientLocale, $localeList)) {
            return $clientLocale;
        }
        return (string) $projectDefault;
    }
}
