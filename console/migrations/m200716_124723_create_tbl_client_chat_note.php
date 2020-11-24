<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200716_124723_create_tbl_client_chat_note
 */
class m200716_124723_create_tbl_client_chat_note extends Migration
{
    private array $routes = [
        '/client-chat-note-crud/index',
        '/client-chat-note-crud/update',
        '/client-chat-note-crud/create',
        '/client-chat-note-crud/delete',
        '/client-chat-note-crud/view',
        '/client-chat/note',
        '/client-chat/create-note',
        '/client-chat/delete-note',
    ];

    private array $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%client_chat_note}}', [
            'ccn_id' => $this->primaryKey(),
            'ccn_chat_id' => $this->integer(),
            'ccn_user_id' => $this->integer(),
            'ccn_note' => $this->text(),
            'ccn_deleted' => $this->boolean()->defaultValue(false),
            'ccn_created_dt' => $this->dateTime(),
            'ccn_updated_dt' => $this->dateTime()
        ], $tableOptions);

        $this->addForeignKey(
            'FK-client_chat_note-client_chat',
            '{{%client_chat_note}}',
            'ccn_chat_id',
            '{{%client_chat}}',
            'cch_id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-client_chat_note-employees',
            '{{%client_chat_note}}',
            'ccn_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createIndex('IND-client_chat_note-ccn_deleted', '{{%client_chat_note}}', ['ccn_deleted']);
        $this->createIndex('IND-client_chat_note-ccn_created_dt', '{{%client_chat_note}}', ['ccn_created_dt']);

        (new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-client_chat_note-ccn_created_dt', '{{%client_chat_note}}');
        $this->dropIndex('IND-client_chat_note-ccn_deleted', '{{%client_chat_note}}');

        $this->dropForeignKey('FK-client_chat_note-client_chat', '{{%client_chat_note}}');
        $this->dropForeignKey('FK-client_chat_note-employees', '{{%client_chat_note}}');

        $this->dropTable('{{%client_chat_note}}');

        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
