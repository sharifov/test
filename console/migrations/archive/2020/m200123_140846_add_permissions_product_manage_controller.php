<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200123_140846_add_permissions_product_manage_controller
 */
class m200123_140846_add_permissions_product_manage_controller extends Migration
{
    public $routes = [
        '/product-manage/create-ajax',
        '/product-manage/delete-ajax',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        if ($permission = $auth->getPermission('/product/create-ajax')) {
            $auth->remove($permission);
        }
        if ($permission = $auth->getPermission('/product/delete-ajax')) {
            $auth->remove($permission);
        }

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
