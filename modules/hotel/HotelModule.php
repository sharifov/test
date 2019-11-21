<?php

namespace modules\hotel;

use yii\helpers\ArrayHelper;

/**
 * hotel module definition class
 *
 * @property \modules\hotel\components\ApiHotelService $apiService The Hotel module.
 */
class HotelModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\hotel\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();



        $config = ArrayHelper::merge(
            require __DIR__ . '/config/config.php',
            require __DIR__ . '/config/config-local.php'
        );

        //\Yii::configure($this, require __DIR__ . '/config.php');
        \Yii::configure($this, $config);


        //$this->controllerNamespace = 'modules\hotel\controllers';
        $this->setViewPath('@modules/hotel/views');
        // custom initialization code goes here
    }

    /**
     * @param string $category
     * @param string $message
     * @param array $params
     * @param null|string $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return \Yii::t('modules/hotel/' . $category, $message, $params, $language);
    }
}
