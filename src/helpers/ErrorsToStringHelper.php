<?php

namespace src\helpers;

use yii\helpers\ArrayHelper;

/**
 * Class ErrorsToStringHelper
 */
class ErrorsToStringHelper
{
    /**
     * @param $model
     * @param string $glue
     * @param bool $withModelName
     * @return string
     */
    public static function extractFromModel($model, string $glue = '<br />', bool $withModelName = false): string
    {
        if (!method_exists($model, 'getErrors')) {
            return '';
        }
        $result = '';
        if ($withModelName && ($modelMane = self::getModelName($model))) {
            $result = $modelMane . ': ';
        }
        return $result . self::extractFromGetErrors($model->getErrors(), $glue);
    }

    public static function extractFromGetErrors(array $errors, string $glue = '<br />'): string
    {
        return implode($glue, ArrayHelper::getColumn($errors, 0, false));
    }

    /**
     * @param $model
     * @return string|null
     */
    private static function getModelName($model): ?string
    {
        try {
            return (new \ReflectionClass($model))->getShortName();
        } catch (\Throwable $throwable) {
            return '';
        }
    }
}
