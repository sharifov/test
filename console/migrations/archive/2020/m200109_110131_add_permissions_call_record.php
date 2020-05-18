<?php

use yii\db\Migration;
use console\migrations\RbacMigrationService;

/**
 * Class m200109_110131_add_permissions_call_record
 */
class m200109_110131_add_permissions_call_record extends Migration
{
    public $routes = [
        '/call/record',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_AGENT,
        \common\models\Employee::ROLE_EX_AGENT,
        \common\models\Employee::ROLE_EX_SUPER,
        \common\models\Employee::ROLE_SUP_AGENT,
        \common\models\Employee::ROLE_SUP_SUPER,
        \common\models\Employee::ROLE_SUPERVISION,
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
