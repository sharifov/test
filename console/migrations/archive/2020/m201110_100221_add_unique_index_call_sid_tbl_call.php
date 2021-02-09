<?php

use yii\db\Migration;

/**
 * Class m201110_100221_add_unique_index_call_sid_tbl_call
 */
class m201110_100221_add_unique_index_call_sid_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('IND-call_c_call_sid', '{{%call}}');
        $this->createIndex('IND-call_c_call_sid', '{{%call}}', ['c_call_sid'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call_c_call_sid', '{{%call}}');
        $this->createIndex('IND-call_c_call_sid', '{{%call}}', ['c_call_sid']);
    }
}
