<?php

namespace modules\product\migrations;

use Yii;
use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200123_152319_create_permissions_module_product
 */
class m200123_152319_create_permissions_module_product extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/product/product/create',
        '/product/product/delete',
        '/product/product/index',
        '/product/product/update',
        '/product/product/view',

        '/product/product-manage/create-ajax',
        '/product/product-manage/delete-ajax',
    ];

    public $oldPermissions = [
        '/product/create',
        '/product/delete',
        '/product/index',
        '/product/update',
        '/product/view',

        '/product-manage/create-ajax',
        '/product-manage/delete-ajax',
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        foreach ($this->oldPermissions as $item) {
            if ($permission = $auth->getPermission($item)) {
                $auth->remove($permission);
            }
        }

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);

        $auth = Yii::$app->authManager;

        foreach ($this->oldPermissions as $item) {
            if ($permission = $auth->createPermission($item)) {
                $auth->add($permission);
                foreach ($this->roles as $role) {
                    $r = $auth->getRole($role);
                    $auth->addChild($r, $permission);
                }
            }
        }
    }
}
