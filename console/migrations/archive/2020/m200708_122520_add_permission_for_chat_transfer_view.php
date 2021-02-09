<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200708_122520_add_permission_for_chat_transfer_view
 */
class m200708_122520_add_permission_for_chat_transfer_view extends Migration
{
    public $route = [
        '/client-chat/ajax-transfer-view',
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
