<?php

namespace console\controllers;

use common\models\Employee;
use sales\rbac\roles\ExchangeSenior;
use sales\rbac\roles\SalesSenior;
use sales\rbac\roles\SupportSenior;
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

    public function actionSyncExchangeSeniorRole(): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        self::syncModifiedAdminRole(Employee::ROLE_EXCHANGE_SENIOR, ExchangeSenior::getExcludePermissions());

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionSyncSupportSeniorRole(): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        self::syncModifiedAdminRole(Employee::ROLE_SUPPORT_SENIOR, SupportSenior::getExcludePermissions());

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
            foreach (self::assignPermissionsToRole($permissions, $role) as $report) {
                echo $report . PHP_EOL;
            }
        } else {
            echo 'cant get role: ' . $roleName . PHP_EOL;
        }
    }

    private static function assignPermissionsToRole(array $permissions, Role $role): array
    {
        $auth = Yii::$app->authManager;

        $report = [];

        foreach ($permissions as $item) {
            if ($permission = $auth->getPermission($item)) {
                if (!$auth->hasChild($role, $permission)) {
                    if ($auth->canAddChild($role, $permission)) {
                        if ($auth->addChild($role, $permission)) {
                            $report[] = 'added: ' . $permission->name;
                        } else {
                            $report[] = 'not added: ' . $permission->name;
                        }
                    } else {
                        $report[] = 'cant add: ' . $permission->name;
                    }
                } else {
                    $report[] = 'permission: ' . $permission->name . ' already assigned';
                }
            } else {
                $report[] = 'cant get permission: ' . $item;
            }
        }

        return $report;
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
