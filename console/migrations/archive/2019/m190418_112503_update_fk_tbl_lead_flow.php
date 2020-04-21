<?php

use yii\db\Migration;

/**
 * Class m190418_112503_update_fk_tbl_lead_flow
 */
class m190418_112503_update_fk_tbl_lead_flow extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-lead_flow-lead', '{{%lead_flow}}');
        $this->addForeignKey('fk-lead_flow-lead', '{{%lead_flow}}', ['lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');

        $this->dropForeignKey('fk-lead_logs-lead', '{{%lead_logs}}');
        $this->addForeignKey('fk-lead_logs-lead', '{{%lead_logs}}', ['lead_id'], '{{%leads}}', ['id'], 'CASCADE', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }


}
