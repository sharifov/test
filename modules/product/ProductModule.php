<?php

namespace modules\product;

use Yii;

class ProductModule extends \yii\base\Module
{
    public $controllerNamespace = 'modules\product\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/product/views');
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
        return Yii::t('modules/product/' . $category, $message, $params, $language);
    }
}
