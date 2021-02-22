<?php

namespace modules\main\traits;

use Yii;
use modules\attraction\AttractionModule;

/**
 * Trait ModuleTrait
 *
 * @property-read AttractionModule $module
 * @package modules\main\traits
 */
trait ModuleTrait
{
    /**
     * @return null|\yii\base\Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('attraction');
    }
}
