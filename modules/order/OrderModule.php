<?php

namespace modules\order;

use Yii;
use yii\base\Module;

class OrderModule extends Module
{
    public $controllerNamespace = 'modules\order\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/order/views');
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
        return Yii::t('modules/order/' . $category, $message, $params, $language);
    }
}
