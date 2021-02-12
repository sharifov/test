<?php

namespace modules\rentCar;

use yii\helpers\ArrayHelper;

/**
 * RentCarModule class
 *
 * @property \modules\rentCar\components\ApiRentCarService $apiService.
 */
class RentCarModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\rentCar\controllers';

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

        \Yii::configure($this, $config);

        $this->setViewPath('@modules/rentCar/views');
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
        return \Yii::t('modules/rentCar/' . $category, $message, $params, $language);
    }

    /**
     * @param string $modulePath
     * @return array
     */
    public static function getListMenu(string $modulePath = 'rent-car'): array
    {
        return [
            /*['label' => 'Main', 'url' => ['/' . $modulePath . '/default/index']],*/
            ['label' => 'Rent Car Request', 'url' => ['/' . $modulePath . '/rent-car-crud/index']],
            ['label' => 'Rent Car Quote', 'url' => ['/' . $modulePath . '/rent-car-quote-crud/index']],
            /*['label' => 'Rent Car Quote Service log', 'url' => ['/' . $modulePath . '/rent-car-quote-service-log-crud/index']],*/
        ];
    }
}
