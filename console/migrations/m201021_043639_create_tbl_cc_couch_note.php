<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m201021_043639_create_tbl_cc_couch_note
 */
class m201021_043639_create_tbl_cc_couch_note extends Migration
{
    private array $route = [
        '/client-chat-couch-note-crud/*',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
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

        $this->createTable('{{%client_chat_couch_note}}', [
            'cccn_id' => $this->primaryKey(),
            'cccn_cch_id' => $this->integer(),
            'cccn_rid' => $this->string(150),
            'cccn_message' => $this->text(),
            'cccn_alias' => $this->string(50),
            'cccn_created_user_id' => $this->integer(),
            'cccn_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-client_chat_couch_note-cccn_cch_id',
            '{{%client_chat_couch_note}}',
            ['cccn_cch_id'],
            '{{%client_chat}}',
            ['cch_id'],
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-client_chat_couch_note-cccn_created_user_id',
            '{{%client_chat_couch_note}}',
            ['cccn_created_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        (new RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_chat_couch_note}}');

        (new RbacMigrationService())->down($this->route, $this->roles);
    }
}
