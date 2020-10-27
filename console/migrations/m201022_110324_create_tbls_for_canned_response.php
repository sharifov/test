<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201022_110324_create_tbls_for_canned_response
 */
class m201022_110324_create_tbls_for_canned_response extends Migration
{
    private $routes = [
        '/client-chat-canned-response-crud/view',
        '/client-chat-canned-response-crud/index',
        '/client-chat-canned-response-crud/create',
        '/client-chat-canned-response-crud/updated',
        '/client-chat-canned-response-crud/delete',

        '/client-chat-canned-response-category-crud/index',
        '/client-chat-canned-response-category-crud/view',
        '/client-chat-canned-response-category-crud/create',
        '/client-chat-canned-response-category-crud/update',
        '/client-chat-canned-response-category-crud/delete',

        'client-chat/canned-response'
    ];

    private $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
    ];

    public function init()
    {
        $this->db = 'db_postgres';
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//        $tableOptions = null;
//        if ($this->db->driverName === 'mysql') {
//            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
//        }
//
        $this->createTable('{{%client_chat_canned_response_category}}', [
            'crc_id' => $this->primaryKey(),
            'crc_name' => $this->string(50)->notNull(),
            'crc_enabled' => $this->boolean(),
            'crc_created_dt' => $this->dateTime(),
            'crc_updated_dt' => $this->dateTime(),
            'crc_created_user_id' => $this->integer(),
            'crc_updated_user_id' => $this->integer()
        ]);

//        $this->addForeignKey('FK-crc_created_user_id', '{{%client_chat_canned_response_category}}', ['crc_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
//        $this->addForeignKey('FK-crc_updated_user_id', '{{%client_chat_canned_response_category}}', ['crc_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

        $this->createTable('{{%client_chat_canned_response}}', [
            'cr_id' => $this->primaryKey(),
            'cr_project_id' => $this->integer()->defaultValue(null),
            'cr_category_id' => $this->integer()->defaultValue(null),
            'cr_language_id' => $this->string(5)->defaultValue(null),
            'cr_user_id' => $this->integer()->defaultValue(null),
            'cr_sort_order' => $this->smallInteger(),
            'cr_message' => $this->text(),
            'cr_created_dt' => $this->dateTime(),
            'cr_updated_dt' => $this->dateTime()
        ]);

//        $this->addForeignKey('FK-cr_project_id', '{{%client_chat_canned_response}}', ['cr_project_id'], '{{%projects}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-cr_category_id', '{{%client_chat_canned_response}}', ['cr_category_id'], '{{%client_chat_canned_response_category}}', ['crc_id'], 'SET NULL', 'CASCADE');
//        $this->addForeignKey('FK-cr_language_id', '{{%client_chat_canned_response}}', ['cr_language_id'], '{{%language}}', ['language_id'], 'SET NULL', 'CASCADE');
//        $this->addForeignKey('FK-cr_user_id', '{{%client_chat_canned_response}}', ['cr_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');


        $auth = Yii::$app->authManager;
        $cannedResponsePermission = $auth->createPermission('client-chat/canned-response');
        $cannedResponsePermission->description = 'Access to canned response';
        $auth->add($cannedResponsePermission);

        (new RbacMigrationService())->up($this->routes, $this->roles);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_chat_canned_response}}');
        $this->dropTable('{{%client_chat_canned_response_category}}');

        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission('client-chat/canned-response');
        $auth->remove($permission);

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
