<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200128_144836_add_permissions_invoice_module
 */
class m200128_144836_add_permissions_invoice_module extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/invoice/invoice-crud/create', '/invoice/invoice-crud/delete', '/invoice/invoice-crud/index', '/invoice/invoice-crud/update', '/invoice/invoice-crud/view',
        '/invoice/invoice/create-ajax', '/invoice/invoice/delete-ajax', '/invoice/invoice/update-ajax',
    ];

    public $oldPermissions = [
        '/invoice/create', '/invoice/create-ajax', '/invoice/delete', '/invoice/delete-ajax', '/invoice/index', '/invoice/update', '/invoice/update-ajax', '/invoice/view',
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
