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

        self::syncModifiedAdminRole(Employee::ROLE_SALES_SENIOR, SalesSenior::getExcludePermissions());

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    private static function syncModifiedAdminRole(string $roleName, array $excludePermissions): void
    {
        $auth = Yii::$app->authManager;

        if ($role = $auth->getRole($roleName)) {
            if ($auth->removeChildren($role)) {
                echo 'old permissions removed' . PHP_EOL;
            } else {
                echo 'old permissions not removed' . PHP_EOL;
            }

            $permissions = array_diff(self::getAllPermissionsFromAdmin(), $excludePermissions);
            self::assignPermissionsToRole($permissions, $role);
        }
    }

    private static function assignPermissionsToRole(array $permissions, Role $role): void
    {
        $auth = Yii::$app->authManager;

        foreach ($permissions as $item) {
            if ($permission = $auth->getPermission($item)) {
                if (!$auth->hasChild($role, $permission)) {
                    if ($auth->canAddChild($role, $permission)) {
                        if ($auth->addChild($role, $permission)) {
                            echo 'added: ' . $permission->name . PHP_EOL;
                        } else {
                            echo 'not added: ' . $permission->name . PHP_EOL;
                        }
                    } else {
                        echo 'cant add: ' . $permission->name . PHP_EOL;
                    }
                } else {
                    echo 'permission: ' . $permission->name . ' already assigned' . PHP_EOL;
                }
            } else {
                echo 'cant get permission: ' . $item . PHP_EOL;
            }
        }
    }

    private static function getAllPermissionsFromAdmin(): array
    {
        $auth = Yii::$app->authManager;

        $permissions = [];
        foreach ($auth->getPermissionsByRole(Employee::ROLE_ADMIN) as $permission) {
            $permissions[] = $permission->name;
        }
        return  $permissions;
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
