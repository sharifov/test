<?php

namespace modules\flight;

use modules\flight\components\api\ApiFlightService;
use yii\helpers\ArrayHelper;

/**
 * hotel module definition class
 *
 * @property ApiFlightService $apiService The Flight module.
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
        return [
            ['label' => 'Main', 'url' => ['/' . $modulePath . '/default/index']],
            ['label' => 'Flight', 'url' => ['/' . $modulePath . '/flight/index']],
            ['label' => 'Flight Segments', 'url' => ['/' . $modulePath . '/flight-segment/index']],
            ['label' => 'Quotes', 'url' => ['/' . $modulePath . '/flight-quote/index']],
            ['label' => 'Quote Trip', 'url' => ['/' . $modulePath . '/flight-quote-trip/index']],
            ['label' => 'Quote Segments', 'url' => ['/' . $modulePath . '/flight-quote-segment/index']],
            ['label' => 'Pax', 'url' => ['/' . $modulePath . '/flight-pax/index']],
            ['label' => 'Pax Price', 'url' => ['/' . $modulePath . '/flight-quote-pax-price/index']],
            ['label' => 'Stops', 'url' => ['/' . $modulePath . '/flight-quote-segment-stop/index']],
            ['label' => 'Baggage', 'url' => ['/' . $modulePath . '/flight-quote-segment-pax-baggage/index']],
            ['label' => 'Baggage charge', 'url' => ['/' . $modulePath . '/flight-quote-segment-pax-baggage-charge/index']],
            ['label' => 'Status Log', 'url' => ['/' . $modulePath . '/flight-quote-status-log/index']],
            ['label' => 'Flight Quote Option', 'url' => ['/' . $modulePath . '/flight-quote-option-crud/index']],
            ['label' => 'Flight Quote Flight', 'url' => ['/' . $modulePath . '/flight-quote-flight-crud/index']],
            ['label' => 'Flight Quote Ticket', 'url' => ['/' . $modulePath . '/flight-quote-ticket-crud/index']],

            ['label' => 'Flight Quote Booking', 'url' => ['/' . $modulePath . '/flight-quote-booking-crud/index']],
            ['label' => 'Flight Quote Booking Airline', 'url' => ['/' . $modulePath . '/flight-quote-booking-airline-crud/index']],
            ['label' => 'Flight Quote Label', 'url' => ['/' . $modulePath . '/flight-quote-label-crud/index']],

            ['label' => 'Flight Request', 'url' => ['/' . $modulePath . '/flight-request-crud/index']],
            ['label' => 'Flight Request Log', 'url' => ['/' . $modulePath . '/flight-request-log-crud/index']],

            ['label' => 'Flight Quote Ticket Refund', 'url' => ['/' . $modulePath . '/flight-quote-ticket-refund-crud/index']],
        ];
    }
}
