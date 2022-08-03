<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220803_073733_add_rbac_permissions_for_client_chat_form_response_table
 */
class m220803_073733_add_rbac_permissions_for_client_chat_form_response_table extends Migration
{
    private $routes = [
        '/client-chat/client-chat-form-response',
        '/client-chat-form-response/index',
        '/client-chat-form-response/create',
        '/client-chat-form-response/update',
        '/client-chat-form-response/delete',
        '/client-chat-form-response/view',
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
    ];

    /**
     * @return false|mixed|void
     * @throws \yii\base\Exception
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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220803_073733_add_rbac_permissions_for_client_chat_form_response_table cannot be reverted.\n";

        return false;
    }
    */
}
