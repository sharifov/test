<?php

namespace modules\shiftSchedule;

use Yii;
use yii\base\Module;

class ShiftScheduleModule extends Module
{
    public $controllerNamespace = 'modules\shiftSchedule\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/shiftSchedule/views');
    }

    /**
     * @param string $category
     * @param string $message
     * @param array $params
     * @param null|string $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null): string
    {
        return Yii::t('modules/shiftSchedule/' . $category, $message, $params, $language);
    }
}
