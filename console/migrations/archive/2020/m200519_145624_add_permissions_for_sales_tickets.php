<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200519_145624_add_permissions_for_sales_tickets
 */
class m200519_145624_add_permissions_for_sales_tickets extends Migration
{
    public $route = [
        '/sale-ticket/index',
        '/sale-ticket/create',
        '/sale-ticket/update',
        '/sale-ticket/delete',
        '/sale-ticket/view',
    ];

    public $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
