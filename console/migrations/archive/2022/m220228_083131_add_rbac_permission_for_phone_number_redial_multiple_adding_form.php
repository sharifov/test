<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220228_083131_add_rbac_permission_for_phone_number_redial_multiple_adding_form
 */
class m220228_083131_add_rbac_permission_for_phone_number_redial_multiple_adding_form extends Migration
{
    private array $routes = [
        '/phone-number-redial-crud/create-multiple'
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
