<?php

declare(strict_types=1);

namespace common\components\validators;

use yii\base\Model;
use yii\validators\Validator;

/**
 * Normalizing the Expiration Date.
 * If the value does not contain a time then the format is taken from the $formatStaticTime property otherwise from $formatDynamicTime
 *
 * @package Validators
 *
 */
class NormalizeDateValidator extends Validator
{
    /**
     * @var string
     */
    public string $formatDynamicTime = 'Y-m-d H:i:s';
    /**
     * @var string
     */
    public string $formatStaticTime = 'Y-m-d 23:59:59';

    /**
     * @param Model $model
     * @param string $attribute
     * @return void
     */
    public function validateAttribute($model, $attribute): void
    {
        $value = trim($model->$attribute);
        $timeUnix = strtotime($value);

        if ($timeUnix) {
            $model->$attribute = date(
                strpos($value, ' ') ? $this->formatDynamicTime : $this->formatStaticTime,
                $timeUnix
            );
        } else {
            $model->$attribute = null;
        }
    }
}
