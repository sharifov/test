<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200529_214515_add_permission_join_to_conference
 */
class m200529_214515_add_permission_join_to_conference extends Migration
{
    public array $route = [
        '/phone/ajax-join-to-conference'
    ];

    public array $oldRoute = [
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
        (new \console\migrations\RbacMigrationService())->down($this->oldRoute, $this->roles);
        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->up($this->oldRoute, $this->roles);
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
