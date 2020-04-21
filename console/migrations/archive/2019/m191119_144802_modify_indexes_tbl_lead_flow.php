<?php

use yii\db\Migration;

/**
 * Class m191119_144802_modify_indexes_tbl_lead_flow
 */
class m191119_144802_modify_indexes_tbl_lead_flow extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('ind-lead_flow_status', '{{%lead_flow}}');
        $this->dropIndex('ind-lead_flow_status_lf_from_status_id', '{{%lead_flow}}');

        $this->createIndex('ind-lead_flow_status', '{{%lead_flow}}', ['lf_owner_id', 'lf_from_status_id', 'status']);
        $this->createIndex('ind-lead_flow_status_lf_from_status_id', '{{%lead_flow}}', ['lf_owner_id', 'lf_from_status_id', 'status']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('ind-lead_flow_status', '{{%lead_flow}}');
        $this->dropIndex('ind-lead_flow_status_lf_from_status_id', '{{%lead_flow}}');

        $this->createIndex('ind-lead_flow_status', '{{%lead_flow}}', ['status']);
        $this->createIndex('ind-lead_flow_status_lf_from_status_id', '{{%lead_flow}}', ['status', 'lf_from_status_id']);
    }
}
