<?php

namespace console\controllers;

use common\models\Employee;
use sales\rbac\roles\SalesSenior;
use Yii;
use console\migrations\RbacMigrationService;
use yii\console\Controller;
use yii\helpers\BaseConsole;
use yii\helpers\Console;
use yii\rbac\Role;

class RbacController extends Controller
{
    public function actionSyncSalesSeniorRole(): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $auth = Yii::$app->authManager;

        if ($role = $auth->getRole(Employee::ROLE_SALES_SENIOR)) {
            if ($auth->removeChildren($role)) {
                $this->addAllPermissionsFromAdmin($role);
                $this->removePermissionsFromRole($role, SalesSenior::getExcludePermissions());
            } else {
                echo 'cant remove old permissions';
            }
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * @param Role $role
     * @param string[] $permissions
     */
    private function removePermissionsFromRole(Role $role, array $permissions): void
    {
        $auth = Yii::$app->authManager;

        foreach ($permissions as $item) {
            if ($permission = $auth->getPermission($item)) {
                if ($auth->hasChild($role, $permission)) {
                    if ($auth->removeChild($role, $permission)) {
                        echo 'removed ' . $item . PHP_EOL;
                    } else {
                        echo 'not removed ' . $item . PHP_EOL;
                    }
                } else {
                    echo 'not found child ' . $item . PHP_EOL;
                }
            }
        }
    }

    private function addAllPermissionsFromAdmin(Role $role): void
    {
        $auth = Yii::$app->authManager;

        foreach ($auth->getPermissionsByRole(Employee::ROLE_ADMIN) as $permission) {
            if ($auth->canAddChild($role, $permission)) {
                if ($auth->addChild($role, $permission)) {
                    echo 'added: ' . $permission->name . PHP_EOL;
                } else {
                    echo 'not added: ' . $permission->name . PHP_EOL;
                }
            } else {
                echo 'cant add: ' . $permission->name . PHP_EOL;
            }
        }
    }

    /**
     * for any role --> from /controller/* to /controller/index  /controller/view ...
     */
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
