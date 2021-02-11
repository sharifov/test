<?php

namespace modules\main\traits;

use Yii;
use modules\rentCar\RentCarModule;

/**
 * Trait ModuleTrait
 *
 * @property-read RentCarModule $module
 */
trait ModuleTrait
{
    /**
     * @return null|\yii\base\Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('rentCar');
    }
}
