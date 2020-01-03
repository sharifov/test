<?php

namespace modules\flight;

use yii\helpers\ArrayHelper;

/**
 * hotel module definition class
 *
 * @property \modules\flight\components\ApiFlightService $apiService The Flight module.
 */
class FlightModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\flight\controllers';

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
        $this->setViewPath('@modules/flight/views');
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
        return \Yii::t('modules/flight/' . $category, $message, $params, $language);
    }


    /**
     * @param string $modulePath
     * @return array
     */
    public static function getListMenu(string $modulePath = 'flight'): array
    {

        $items = [
            ['label' => 'Main', 'url' => ['/'. $modulePath .'/default/index']],
            ['label' => 'Flight Request', 'url' => ['/'. $modulePath .'/flight/index']],
        ];
        return $items;
    }
}
