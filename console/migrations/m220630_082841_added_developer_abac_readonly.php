<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m220630_082841_added_developer_abac_readonly
 */
class m220630_082841_added_developer_abac_readonly extends Migration
{
    private array $routes = [
        '/abac/abac-policy/index',
        '/abac/abac-policy/view',
        '/abac/abac-doc/index',
        '/abac/abac-policy/list-content'
    ];

    private array $roles = [
        'developer',
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
