<?php

namespace modules\cruise;

use yii\helpers\ArrayHelper;

class CruiseModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\cruise\controllers';

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
        $this->setViewPath('@modules/cruise/views');
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
        return \Yii::t('modules/cruise/' . $category, $message, $params, $language);
    }

    /**
     * @param string $modulePath
     * @return array
     */
    public static function getListMenu(string $modulePath = 'cruise'): array
    {
        $items = [
            ['label' => 'Main', 'url' => ['/' . $modulePath . '/default/index']],
            ['label' => 'Request', 'url' => ['/' . $modulePath . '/cruise/index']],
            ['label' => 'Request Cabin', 'url' => ['/' . $modulePath . '/cruise-cabin/index']],
            ['label' => 'Request Cabin Rooms Pax', 'url' => ['/' . $modulePath . '/cruise-cabin-pax/index']],
            ['label' => 'Quotes', 'url' => ['/' . $modulePath . '/cruise-quote/index']],
//            ['label' => 'Hotel Quote', 'url' => ['/' . $modulePath . '/hotel-quote/index']],
//            ['label' => 'Hotel Quote Rooms', 'url' => ['/' . $modulePath . '/hotel-quote-room/index']],
//            ['label' => 'Hotel Quote Service log', 'url' => ['/' . $modulePath . '/hotel-quote-service-log-crud/index']],
//            ['label' => 'Hotel List', 'url' => ['/' . $modulePath . '/hotel-list/index']],
        ];
        return $items;
    }
}
