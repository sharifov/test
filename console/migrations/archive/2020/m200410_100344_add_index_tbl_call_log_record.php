<?php

use yii\db\Migration;

/**
 * Class m200410_100344_add_index_tbl_call_log_record
 */
class m200410_100344_add_index_tbl_call_log_record extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%call_log_record}}', 'clr_record_sid', $this->string(34)->notNull());
        $this->createIndex('IND-call_log_record-clr_record_sid', '{{%call_log_record}}', ['clr_record_sid']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%call_log_record}}', 'clr_record_sid', $this->string(34)->null());
        $this->dropIndex('IND-call_log_record-clr_record_sid', '{{%call_log_record}}');
    }
}
