<?php

use yii\db\Migration;

/**
 * Class m210929_063757_alter_tbl_call_log_filter_guard_add_new_column
 */
class m210929_063757_alter_tbl_call_log_filter_guard_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call_log_filter_guard}}', 'clfg_redial_status', $this->tinyInteger(2));
        $this->createIndex('IND-call_log_filter_guard-clfg_redial_status', '{{%call_log_filter_guard}}', 'clfg_redial_status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%call_log_filter_guard}}', 'clfg_redial_status');
    }
}
