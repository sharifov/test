<?php

namespace modules\offer;

use Yii;
use yii\base\Module;

class OfferModule extends Module
{
    public $controllerNamespace = 'modules\offer\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/offer/views');
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
        return Yii::t('modules/offer/' . $category, $message, $params, $language);
    }
}
