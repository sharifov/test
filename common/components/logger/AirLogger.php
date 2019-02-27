<?php
/**
 * Created by PhpStorm.
 * User: dmitrii
 * Date: 1/21/19
 * Time: 6:04 PM
 */

namespace common\components\logger;
use Yii;

class AirLogger
{
    public static function debug($message, $category = 'application', $fields = [])
    {
        if ($fields === []) {
            Yii::debug($message, $category);
            return;
        }

        $fields["@message"] = $message;
        Yii::debug($message, $category);
    }

    public static function trace($message, $category = 'application')
    {
        static::debug($message, $category);
    }

    public static function error($message, $category = 'application', $fields = [])
    {
        if ($fields === []) {
            Yii::error($message, $category);
            return;
        }

        $fields["@message"] = $message;
        Yii::error($fields, $category);
    }

    public static function warning($message, $category = 'application', $fields = [])
    {
        if ($fields === []) {
            Yii::warning($message, $category);
            return;
        }

        $fields["@message"] = $message;
        Yii::warning($fields, $category);
    }

    public static function info($message, $category = 'application', $fields = [])
    {
        if ($fields === []) {
            Yii::info($message, $category);
            return;
        }

        $fields["@message"] = $message;
        Yii::info($fields, $category);
    }

    public static function beginProfile($token, $category = 'application')
    {
        Yii::beginProfile($token, $category);
    }

    public static function endProfile($token, $category = 'application')
    {
        Yii::endProfile($token, $category);
    }
}