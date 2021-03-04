<?php

namespace modules\attraction;

use modules\attraction\components\ApiAttractionService;
use yii\helpers\ArrayHelper;

/**
 * attraction module definition class
 *
 * @property ApiAttractionService $apiService The Attraction module.
 */
class AttractionModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'modules\attraction\controllers';

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
        $this->setViewPath('@modules/attraction/views');
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
        return \Yii::t('modules/attraction/' . $category, $message, $params, $language);
    }


    /**
     * @param string $modulePath
     * @return array
     */
    public static function getListMenu(string $modulePath = 'attraction'): array
    {
        $items = [
            ['label' => 'Main', 'url' => ['/' . $modulePath . '/default/index']],
            ['label' => 'Attraction Request', 'url' => ['/' . $modulePath . '/attraction/index']],
            /*['label' => 'Flight Segments', 'url' => ['/' . $modulePath . '/flight-segment/index']],
            ['label' => 'Quotes', 'url' => ['/' . $modulePath . '/flight-quote/index']],
            ['label' => 'Quote Trip', 'url' => ['/' . $modulePath . '/flight-quote-trip/index']],
            ['label' => 'Quote Segments', 'url' => ['/' . $modulePath . '/flight-quote-segment/index']],
            ['label' => 'Pax', 'url' => ['/' . $modulePath . '/flight-pax/index']],
            ['label' => 'Pax Price', 'url' => ['/' . $modulePath . '/flight-quote-pax-price/index']],
            ['label' => 'Stops', 'url' => ['/' . $modulePath . '/flight-quote-segment-stop/index']],
            ['label' => 'Baggage', 'url' => ['/' . $modulePath . '/flight-quote-segment-pax-baggage/index']],
            ['label' => 'Baggage charge', 'url' => ['/' . $modulePath . '/flight-quote-segment-pax-baggage-charge/index']],
            ['label' => 'Status Log', 'url' => ['/' . $modulePath . '/flight-quote-status-log/index']],*/
        ];

        return $items;
    }
}
