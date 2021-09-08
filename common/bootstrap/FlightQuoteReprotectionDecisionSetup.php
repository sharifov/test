<?php

namespace common\bootstrap;

use modules\flight\src\useCases\reprotectionDecision\BoCustomerDecisionService;
use modules\flight\src\useCases\reprotectionDecision\CustomerDecisionService;
use modules\flight\src\useCases\reprotectionDecision\FakeErrorCustomerDecisionService;
use modules\flight\src\useCases\reprotectionDecision\FakeSuccessCustomerDecisionService;
use yii\base\BootstrapInterface;

class FlightQuoteReprotectionDecisionSetup implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;

        $container->setSingleton(CustomerDecisionService::class, static function () {
            return new BoCustomerDecisionService();
//            return new FakeSuccessCustomerDecisionService();
//            return new FakeErrorCustomerDecisionService();
        });
    }
}
