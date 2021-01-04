<?php

use yii\db\Migration;

/**
 * Class m210104_113445_add_permissions_call_recording_disabled_list
 */
class m210104_113445_add_permissions_call_recording_disabled_list extends Migration
{
    private $route = [
        '/call-recording-disabled/list'
    ];

    private $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN
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
