<?php

namespace modules\main\traits;

use Yii;
use modules\hotel\HotelModule;

/**
 * Trait ModuleTrait
 *
 * @property-read HotelModule $module
 * @package modules\main\traits
 */
trait ModuleTrait
{
    /**
     * @return null|\yii\base\Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('hotel');
    }
}