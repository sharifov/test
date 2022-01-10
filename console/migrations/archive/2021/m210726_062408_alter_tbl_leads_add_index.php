<?php

use yii\db\Migration;

/**
 * Class m210726_062408_alter_tbl_leads_add_index
 */
class m210726_062408_alter_tbl_leads_add_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-leads-status', '{{%leads}}', 'status');
        $this->createIndex('IND-lead_flow-status', '{{%lead_flow}}', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-leads-status', '{{%leads}}');
        $this->dropIndex('IND-lead_flow-status', '{{%lead_flow}}');
    }
}
