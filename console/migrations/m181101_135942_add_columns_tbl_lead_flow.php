<?php

use yii\db\Migration;

/**
 * Class m181101_135942_add_columns_tbl_lead_flow
 */
class m181101_135942_add_columns_tbl_lead_flow extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_flow}}', 'lf_from_status_id', $this->integer());
        $this->addColumn('{{%lead_flow}}', 'lf_end_dt', $this->integer());
        $this->addColumn('{{%lead_flow}}', 'lf_time_period', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%lead_flow}}', 'lf_from_status_id');
        $this->dropColumn('{{%lead_flow}}', 'lf_end_dt');
        $this->dropColumn('{{%lead_flow}}', 'lf_time_period');
    }

}
