<?php

namespace modules\hotel;

/**
 * hotel module definition class
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
        return \Yii::t('modules/main/' . $category, $message, $params, $language);
    }
}
