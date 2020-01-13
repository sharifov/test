<?php

namespace console\controllers;

use Yii;
use console\migrations\RbacMigrationService;
use yii\console\Controller;
use yii\helpers\BaseConsole;
use yii\helpers\Console;

class RbacController extends Controller
{
    public function actionChangeGroupRouteToRoutes(): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $roleName = BaseConsole::input('Enter Role name: ');

        $report = (Yii::createObject(RbacMigrationService::class))->changeGroupRouteToRoutes($roleName);
        foreach ($report as $item) {
            echo $item . PHP_EOL;
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
}
