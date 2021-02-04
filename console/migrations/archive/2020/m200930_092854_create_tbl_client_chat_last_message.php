<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200930_092854_cteate_tbl_client_chat_last_message
 */
class m200930_092854_create_tbl_client_chat_last_message extends Migration
{
    public $route = [
        '/client-chat-last-message-crud/*',
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

        $this->createTable('{{%client_chat_last_message}}', [
            'cclm_id' => $this->primaryKey(),
            'cclm_cch_id' => $this->integer(),
            'cclm_type_id'    => $this->tinyInteger(),
            'cclm_message' => $this->text(),
            'cclm_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->createIndex('IND-client_chat_last_message-cclm_type_id', '{{%client_chat_last_message}}', ['cclm_type_id']);
        $this->addCommentOnColumn('{{%client_chat_last_message}}', 'cclm_type_id', '1 - client, 2 - user');

        $this->addForeignKey(
            'FK-client_chat_last_message-cclm_cch_id',
            '{{%client_chat_last_message}}',
            ['cclm_cch_id'],
            '{{%client_chat}}',
            ['cch_id'],
            'CASCADE',
            'CASCADE'
        );

        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_chat_last_message}}');

        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
