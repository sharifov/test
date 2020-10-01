<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201001_093442_create_tbl_client_chat_hold
 */
class m201001_093442_create_tbl_client_chat_hold extends Migration
{
    public $route = [
        '/client-chat-hold-crud/index',
        '/client-chat-hold-crud/view',
        '/client-chat-hold-crud/create',
        '/client-chat-hold-crud/update',
        '/client-chat-hold-crud/delete'
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
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%client_chat_hold}}', [
            'cchd_id' => $this->primaryKey(),
            'cchd_cch_id' => $this->integer()->notNull(),
            'cchd_cch_status_log_id'    => $this->integer(),
            'cchd_deadline_dt' => $this->dateTime()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('FK-client_chat_hold-cchd_cch_id', '{{%client_chat_hold}}', ['cchd_cch_id'],
        '{{%client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');

        $this->addForeignKey('FK-client_chat_hold-cchd_cch_status_log_id', '{{%client_chat_hold}}', ['cchd_cch_status_log_id'],
        '{{%client_chat_status_log}}', ['csl_id'], 'SET NULL', 'CASCADE');

        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_chat_hold}}');

        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
