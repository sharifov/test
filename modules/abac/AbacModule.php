<?php

namespace modules\abac;

use Yii;
use yii\base\Module;

class AbacModule extends Module
{
    public $controllerNamespace = 'modules\abac\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/abac/views');
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
        return Yii::t('modules/abac/' . $category, $message, $params, $language);
    }
}
