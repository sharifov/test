<?php

use yii\db\Migration;

/**
 * Class m210902_071442_add_column_stir_status_to_call
 */
class m210902_071442_add_column_stir_status_to_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_stir_status', $this->string(1)->null());
        $this->createIndex('IND-call-c_stir_status', '{{%call}}', ['c_stir_status']);

        $this->addColumn('{{%call_log}}', 'cl_stir_status', $this->string(1)->null());
        $this->createIndex('IND-call_log-cl_stir_status', '{{%call_log}}', ['cl_stir_status']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call-c_stir_status', '{{%call}}');
        $this->dropColumn('{{%call}}', 'c_stir_status');

        $this->dropIndex('IND-call_log-cl_stir_status', '{{%call_log}}');
        $this->dropColumn('{{%call_log}}', 'cl_stir_status');
    }
}
