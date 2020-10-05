<?php

use yii\db\Migration;

/**
 * Class m201005_080849_create_tbl_user_connection_active_chat
 */
class m201005_080849_create_tbl_user_connection_active_chat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_connection_active_chat}}', [
            'ucac_conn_id' => $this->bigInteger(),
            'ucac_chat_id' => $this->integer(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-user_connection_active_chat-conn-chat-id', '{{%user_connection_active_chat}}', ['ucac_conn_id', 'ucac_chat_id']);
        $this->addForeignKey('FK-user_connection_active_chat-ucac_conn_id', '{{%user_connection_active_chat}}', ['ucac_conn_id'], '{{%user_connection}}', ['uc_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-user_connection_active_chat-ucac_chat_id', '{{%user_connection_active_chat}}', ['ucac_chat_id'], '{{%client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-user_connection_active_chat-ucac_chat_id', '{{%user_connection_active_chat}}');
        $this->dropForeignKey('FK-user_connection_active_chat-ucac_conn_id', '{{%user_connection_active_chat}}');
        $this->dropPrimaryKey('PK-user_connection_active_chat-conn-chat-id', '{{%user_connection_active_chat}}');
        $this->dropTable('{{%user_connection_active_chat}}');
    }
}
