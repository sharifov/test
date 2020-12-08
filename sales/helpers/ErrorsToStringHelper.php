<?php

namespace sales\helpers;

use yii\helpers\ArrayHelper;

/**
 * Class ErrorsToStringHelper
 */
class ErrorsToStringHelper
{
    /**
     * @param $model
     * @param string $glue
     * @return string
     */
    public static function extractFromModel($model, string $glue = '<br />'): string
    {
        if (!method_exists($model, 'getErrors')) {
            return '';
        }
        return self::extractFromGetErrors($model->getErrors(), $glue);
    }

    public static function extractFromGetErrors(array $errors, string $glue = '<br />'): string
    {
        return implode($glue, ArrayHelper::getColumn($errors, 0, false));
    }
}
