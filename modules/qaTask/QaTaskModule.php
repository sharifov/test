<?php

namespace modules\qaTask;

use Yii;
use yii\base\Module;

class QaTaskModule extends Module
{
    public $controllerNamespace = 'modules\qaTask\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/qaTask/views');
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
        return Yii::t('modules/qaTask/' . $category, $message, $params, $language);
    }
}
