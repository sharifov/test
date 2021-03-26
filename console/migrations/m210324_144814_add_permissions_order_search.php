<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210324_144814_add_permissions_order_search
 */
class m210324_144814_add_permissions_order_search extends Migration
{
    private array $route = [
        '/order/order/search'
    ];

    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN
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
