<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210126_143551_add_permission_lead_alternative
 */
class m210126_143551_add_permission_lead_alternative extends Migration
{
    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN
    ];

    public $routes = [
        '/lead/alternative',
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
