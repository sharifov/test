<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201019_143459_add_permission_for_deletion_client_chat_project_configs_cache
 */
class m201019_143459_add_permission_for_deletion_client_chat_project_configs_cache extends Migration
{
    private $route = [
        '/client-chat-project-config/delete-cache'
    ];

    private $roles = [
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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_143459_add_permission_for_deletion_client_chat_project_configs_cache cannot be reverted.\n";

        return false;
    }
    */
}
