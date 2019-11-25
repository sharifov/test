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


    /**
     * @param string $modulePath
     * @return array
     */
    public static function getListMenu(string $modulePath = 'hotel'): array
    {

        $items = [
            ['label' => 'Main', 'url' => ['/'. $modulePath .'/default/index']],
            ['label' => 'Hotel Request', 'url' => ['/'. $modulePath .'/hotel/index']],
            ['label' => 'Hotel Request Rooms', 'url' => ['/'. $modulePath .'/hotel-room/index']],
            ['label' => 'Hotel Request Rooms Pax', 'url' => ['/'. $modulePath .'/hotel-room-pax/index']],
            ['label' => 'Hotel Quote', 'url' => ['/'. $modulePath .'/hotel-quote/index']],
            ['label' => 'Hotel Quote Rooms', 'url' => ['/'. $modulePath .'/hotel-quote-room/index']],
            ['label' => 'Hotel List', 'url' => ['/'. $modulePath .'/hotel-list/index']],
        ];
        return $items;
    }
}
