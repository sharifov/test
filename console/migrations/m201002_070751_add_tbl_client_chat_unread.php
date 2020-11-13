<?php

use yii\db\Migration;

/**
 * Class m201002_070751_add_tbl_client_chat_unread
 */
class m201002_070751_add_tbl_client_chat_unread extends Migration
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

        $this->createTable('{{%client_chat_unread}}', [
            'ccu_cc_id' => $this->integer(),
            'ccu_count' => $this->integer(),
            'ccu_created_dt' => $this->dateTime(),
            'ccu_updated_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-client_chat_unread-ccu_cc_id', '{{%client_chat_unread}}', ['ccu_cc_id']);
        $this->addForeignKey('FK-client_chat_unread-ccu_cc_id', '{{%client_chat_unread}}', ['ccu_cc_id'], '{{%client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');
        $this->createIndex('IND-client_chat_unread-ccu_created_dt', '{{%client_chat_unread}}', ['ccu_created_dt']);
        $this->createIndex('IND-client_chat_unread-ccu_updated_dt', '{{%client_chat_unread}}', ['ccu_updated_dt']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_chat_unread-ccu_cc_id', '{{%client_chat_unread}}');
        $this->dropPrimaryKey('PK-client_chat_unread-ccu_cc_id', '{{%client_chat_unread}}');
        $this->dropIndex('IND-client_chat_unread-ccu_created_dt', '{{%client_chat_unread}}');
        $this->dropIndex('IND-client_chat_unread-ccu_updated_dt', '{{%client_chat_unread}}');
        $this->dropTable('{{%client_chat_unread}}');
    }
}
