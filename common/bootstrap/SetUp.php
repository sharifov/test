<?php

namespace common\bootstrap;

use sales\services\log\GlobalLogDBService;
use sales\logger\db\GlobalLogInterface;
use yii\base\BootstrapInterface;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = \Yii::$container;

        $container->set(GlobalLogInterface::class, GlobalLogDBService::class);
    }
}
