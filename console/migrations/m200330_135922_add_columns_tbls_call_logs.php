<?php

use yii\db\Migration;

/**
 * Class m200330_135922_add_columns_tbls_call_logs
 */
class m200330_135922_add_columns_tbls_call_logs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call_log_lead}}', 'cll_lead_flow_id', $this->integer()->null());
        $this->addForeignKey(
            'FK-call_log_lead-cll_lead_flow_id',
            '{{%call_log_lead}}',
            'cll_lead_flow_id',
            '{{%lead_flow}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addColumn('{{%call_log_case}}', 'clc_case_status_log_id', $this->integer()->null());
        $this->addForeignKey(
            'FK-call_log_case-clc_case_status_log_id',
            '{{%call_log_case}}',
            'clc_case_status_log_id',
            '{{%case_status_log}}',
            'csl_id',
            'SET NULL',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropForeignKey('FK-call_log_case-clc_case_status_log_id','{{%call_log_case}}');
       $this->dropColumn('{{%call_log_case}}', 'clc_case_status_log_id');
       $this->dropForeignKey('FK-call_log_lead-cll_lead_flow_id','{{%call_log_lead}}');
       $this->dropColumn('{{%call_log_lead}}', 'cll_lead_flow_id');
    }
}
