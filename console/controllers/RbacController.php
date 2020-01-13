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

        $service = Yii::createObject(RbacMigrationService::class);

        $auth = $service->getAuth();

        if ($role = $auth->getRole($roleName)) {

            foreach ($service->getAllGroupByRole($role) as $group) {
                foreach ($service->getAllRoutesByGroup($group) as $route) {
                    $permission = $service->getOrCreatePermission($route);
                    if (!$auth->hasChild($role, $permission)) {
                        if ($auth->canAddChild($role, $permission)) {
                            if ($auth->addChild($role, $permission)) {
                                echo 'added: ' . $route . PHP_EOL;
                            } else {
                                echo 'not added: ' . $route . PHP_EOL;
                            }
                        } else {
                            echo 'cant add: ' . $route . PHP_EOL;
                        }
                    }
                }
                if ($groupRole = $auth->getPermission($group)) {
                    if ($auth->removeChild($role, $groupRole)) {
                        echo 'removed: ' . $group . PHP_EOL;
                    } else {
                        echo 'not removed: ' . $group . PHP_EOL;
                    }
                }
            }
        } else {
            echo 'Not found role: ' . $roleName . PHP_EOL;
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
}
