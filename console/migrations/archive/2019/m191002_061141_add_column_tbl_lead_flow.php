<?php

use yii\db\Migration;

/**
 * Class m191002_061141_add_column_tbl_lead_flow
 */
class m191002_061141_add_column_tbl_lead_flow extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_flow}}', 'lf_out_calls', $this->smallInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%lead_flow}}', 'lf_out_calls');
    }


}
