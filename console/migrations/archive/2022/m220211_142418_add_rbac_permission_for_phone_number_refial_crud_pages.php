<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m220211_142418_add_rbac_permission_for_phone_number_refial_crud_pages
 */
class m220211_142418_add_rbac_permission_for_phone_number_refial_crud_pages extends Migration
{
    private array $routes = [
        '/phone-number-redial-crud/index',
        '/phone-number-redial-crud/view',
        '/phone-number-redial-crud/create',
        '/phone-number-redial-crud/update',
        '/phone-number-redial-crud/delete',
    ];

    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
    }
}
