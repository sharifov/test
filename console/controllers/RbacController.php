<?php

namespace console\controllers;

use common\models\Employee;
use src\rbac\RbacMoveToSrc;
use src\rbac\roles\ExchangeSenior;
use src\rbac\roles\SalesSenior;
use src\rbac\roles\SupportSenior;
use Yii;
use console\migrations\RbacMigrationService;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseConsole;
use yii\helpers\Console;
use yii\helpers\VarDumper;
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

    public function actionMoveRulesToSrc()
    {
        $subDir = 'src';
        (new RbacMoveToSrc())->move(env('APP_PATH') . DIRECTORY_SEPARATOR . $subDir . DIRECTORY_SEPARATOR . 'rbac', $subDir);
        Yii::$app->authManager->invalidateCache();
    }

    public function actionMoveRulesToSales()
    {
        $subDir = 'sales';
        (new RbacMoveToSrc())->move(env('APP_PATH') . DIRECTORY_SEPARATOR . $subDir . DIRECTORY_SEPARATOR . 'rbac', $subDir);
        Yii::$app->authManager->invalidateCache();
    }

    public function actionCheckDifferentNamesAndClassNames()
    {
        $rules = (new Query())->select(['name', 'data'])->from('auth_rule')->all();
        foreach ($rules as $rule) {
            if (strpos($rule['data'], $rule['name']) === false) {
                VarDumper::dump($rule);
            }
        }
    }

    public function actionCheckSalesNamespaceInRules()
    {
        $rules = (new Query())->select(['name', 'data'])->from('auth_rule')->all();
        foreach ($rules as $rule) {
            if (strpos($rule['data'], 'sales') !== false) {
                VarDumper::dump($rule);
            }
        }
    }
}
