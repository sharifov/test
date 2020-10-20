<?php

use yii\db\Migration;

/**
 * Class m201020_104207_add_column_ccm_event
 */
class m201020_104207_add_column_ccm_event extends Migration
{
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
        $this->addColumn('{{%client_chat_message}}', 'ccm_event', $this->tinyInteger(2));
        $this->createIndex('IND-client_chat_message-ccm_cch_id', '{{%client_chat_message}}', ['ccm_cch_id']);
        $this->createIndex('IND-client_chat_message-ccm_rid', '{{%client_chat_message}}', ['ccm_rid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_chat_message}}', 'ccm_event');
        $this->dropIndex('IND-client_chat_message-ccm_cch_id', '{{%client_chat_message}}');
        $this->dropIndex('IND-client_chat_message-ccm_rid', '{{%client_chat_message}}');
    }
}
