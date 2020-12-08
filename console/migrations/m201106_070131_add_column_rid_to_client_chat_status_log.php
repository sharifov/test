<?php

use yii\db\Migration;

/**
 * Class m201106_070131_add_column_rid_to_client_chat_status_log
 */
class m201106_070131_add_column_rid_to_client_chat_status_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_chat_status_log}}', 'csl_rid', $this->string(150));
        $this->createIndex('IND-client_chat_status_log-csl_rid', '{{%client_chat_status_log}}', ['csl_rid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-client_chat_status_log-csl_rid', '{{%client_chat_status_log}}');
        $this->dropColumn('{{%client_chat_status_log}}', 'csl_rid');
    }
}
