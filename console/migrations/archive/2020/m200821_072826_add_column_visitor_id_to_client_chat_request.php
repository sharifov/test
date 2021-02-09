<?php

use yii\db\Migration;

/**
 * Class m200821_072826_add_column_visitor_id_to_client_chat_request
 */
class m200821_072826_add_column_visitor_id_to_client_chat_request extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%client_chat_request}}', 'ccr_visitor_id', $this->string(100));
        $this->createIndex('IND-client_chat_request-ccr_visitor_id', '{{%client_chat_request}}', 'ccr_visitor_id', false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-client_chat_request-ccr_visitor_id', '{{%client_chat_request}}');
        $this->dropColumn('{{%client_chat_request}}', 'ccr_visitor_id');
    }
}
