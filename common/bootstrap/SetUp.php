<?php

namespace common\bootstrap;

use sales\access\DepartmentAccessService;
use sales\access\ProjectAccessService;
use sales\services\log\GlobalLogDBService;
use sales\logger\db\GlobalLogInterface;
use yii\base\BootstrapInterface;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = \Yii::$container;

        $container->set(GlobalLogInterface::class, GlobalLogDBService::class);
        $container->set(ProjectAccessService::class, static function () use ($app) {
            return new ProjectAccessService($app->getAuthManager());
        });
        $container->set(DepartmentAccessService::class, static function () use ($app) {
            return new DepartmentAccessService($app->getAuthManager());
        });
    }
}
