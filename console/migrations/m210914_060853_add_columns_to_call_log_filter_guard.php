<?php

use yii\db\Migration;

/**
 * Class m210914_060853_add_columns_to_call_log_filter_guard
 */
class m210914_060853_add_columns_to_call_log_filter_guard extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call_log_filter_guard}}', 'clfg_created_dt', $this->dateTime());
        $this->addColumn('{{%call_log_filter_guard}}', 'clfg_cpl_id', $this->integer());

        $this->addForeignKey(
            'FK-call_log_filter_guard-clfg_cpl_id',
            '{{%call_log_filter_guard}}',
            'clfg_cpl_id',
            '{{%contact_phone_list}}',
            'cpl_id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-call_log_filter_guard-clfg_cpl_id', '{{%call_log_filter_guard}}');
        $this->dropColumn('{{%call_log_filter_guard}}', 'clfg_cpl_id');
        $this->dropColumn('{{%call_log_filter_guard}}', 'clfg_created_dt');
    }
}
