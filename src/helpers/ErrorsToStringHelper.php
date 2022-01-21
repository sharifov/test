<?php

namespace src\helpers;

use src\helpers\app\AppHelper;
use yii\base\Model;
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
    public static function extractFromModel($model, string $glue = '<br />', bool $withModelName = true): string
    {
        if (!($model instanceof Model)) {
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

    private static function getModelName(Model $model): ?string
    {
        try {
            return (new \ReflectionClass($model))->getShortName();
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'ErrorsToStringHelper:getModelName');
            return '';
        }
    }
}
