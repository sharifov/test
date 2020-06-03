<?php

use yii\db\Migration;

/**
 * Class m200602_071812_create_index_tbl_call_log
 */
class m200602_071812_create_index_tbl_call_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-call_log-cl_client_id', '{{%call_log}}', ['cl_client_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call_log-cl_client_id', '{{%call_log}}');
    }
}
