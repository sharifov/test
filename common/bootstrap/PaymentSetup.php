<?php

namespace common\bootstrap;

use modules\order\src\payment\services\PaymentService;
use modules\order\src\payment\services\SimplePaymentService;
use yii\base\BootstrapInterface;

class PaymentSetup implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;

        $container->set(PaymentService::class, static function () {
            return new SimplePaymentService();
        });
    }
}
