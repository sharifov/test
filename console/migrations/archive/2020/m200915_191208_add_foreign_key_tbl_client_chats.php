<?php

use yii\db\Migration;

/**
 * Class m200915_191208_add_foreign_key_tbl_client_chats
 */
class m200915_191208_add_foreign_key_tbl_client_chats extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey('FK-client_chat_case-cccs_chat_id', '{{%client_chat_case}}', ['cccs_chat_id'], '{{%client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-client_chat_lead-ccl_chat_id', '{{%client_chat_lead}}', ['ccl_chat_id'], '{{%client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');
        $this->addForeignKey('FK-client_chat_note-ccn_chat_id', '{{%client_chat_note}}', ['ccn_chat_id'], '{{%client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_chat_case-cccs_chat_id', '{{%client_chat_case}}');
        $this->dropForeignKey('FK-client_chat_lead-ccl_chat_id', '{{%client_chat_lead}}');
        $this->dropForeignKey('FK-client_chat_note-ccn_chat_id', '{{%client_chat_note}}');
    }
}
