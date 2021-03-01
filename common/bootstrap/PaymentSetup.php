<?php

namespace common\bootstrap;

use modules\order\src\payment\services\PaymentDummyService;
use modules\order\src\payment\services\PaymentService;
use yii\base\BootstrapInterface;

class PaymentSetup implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;

        $container->set(PaymentService::class, static function () {
            return new PaymentDummyService();
        });
    }
}
