<?php

use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m191206_150039_add_permissions_phones_blacklist
 */
class m191206_150039_add_permissions_phones_blacklist extends Migration
{
    public $routes = [
        '/phone-blacklist/*',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
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
