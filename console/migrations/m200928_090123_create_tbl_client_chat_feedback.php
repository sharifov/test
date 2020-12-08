<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200928_090123_create_tbl_client_chat_feedback
 */
class m200928_090123_create_tbl_client_chat_feedback extends Migration
{
    public $route = [
        '/client-chat-feedback-crud/*',
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

        $this->createTable('{{%client_chat_feedback}}', [
            'ccf_id' => $this->primaryKey(),
            'ccf_client_chat_id' => $this->integer(),
            'ccf_user_id'    => $this->integer(),
            'ccf_client_id' => $this->integer(),
            'ccf_rating' => $this->tinyInteger(),
            'ccf_message' => $this->text(),
            'ccf_created_dt' => $this->dateTime(),
            'ccf_updated_dt' => $this->dateTime(),
        ], $tableOptions);


        $this->addForeignKey(
            'FK-client_chat_feedback-ccf_client_chat_id',
            '{{%client_chat_feedback}}',
            ['ccf_client_chat_id'],
            '{{%client_chat}}',
            ['cch_id'],
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-client_chat_feedback-ccf_user_id',
            '{{%client_chat_feedback}}',
            ['ccf_user_id'],
            '{{%employees}}',
            ['id'],
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-client_chat_feedback-ccf_client_id',
            '{{%client_chat_feedback}}',
            ['ccf_client_id'],
            '{{%clients}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_chat_feedback}}');

        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
