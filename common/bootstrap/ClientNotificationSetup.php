<?php

namespace common\bootstrap;

use sales\model\client\notifications\settings\ClientNotificationProjectSettings;
use sales\model\client\notifications\settings\ClientNotificationSimpleProjectSettings;
use yii\base\BootstrapInterface;

class ClientNotificationSetup implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;

        $container->setSingleton(ClientNotificationProjectSettings::class, static function () {
            return new ClientNotificationSimpleProjectSettings();
        });
    }
}
