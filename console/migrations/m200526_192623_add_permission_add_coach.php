<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200526_192623_add_permission_add_coach
 */
class m200526_192623_add_permission_add_coach extends Migration
{
    public array $route = [
        '/phone/ajax-add-coach'
    ];

    public array $roles = [
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
