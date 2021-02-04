<?php

use yii\db\Migration;

/**
 * Class m201027_095141_remove_fk_client_chat_note_client_chat_tbl_cc_note
 */
class m201027_095141_remove_fk_client_chat_note_client_chat_tbl_cc_note extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-client_chat_note-client_chat', '{{%client_chat_note}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addForeignKey(
            'FK-client_chat_note-client_chat',
            '{{%client_chat_note}}',
            'ccn_chat_id',
            '{{%client_chat}}',
            'cch_id',
            'SET NULL',
            'CASCADE'
        );
    }
}
