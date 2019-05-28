<?php

use yii\db\Migration;

/**
 * Class m190528_055001_add_column_last_action_dt_tbl_leads
 */
class m190528_055001_add_column_last_action_dt_tbl_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'l_last_action_dt', $this->dateTime());
        $this->createIndex('IND-leads_l_last_action_dt', '{{%leads}}', ['l_last_action_dt']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-leads_l_last_action_dt', '{{%leads}}');
        $this->dropColumn('{{%leads}}', 'l_last_action_dt');
    }
}
