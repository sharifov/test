<?php

use yii\db\Migration;

/**
 * Class m190618_060202_remove_indexes
 */
class m190618_060202_remove_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('IND-call_c_from', '{{%call}}');
        $this->dropIndex('IND-call_c_to', '{{%call}}');
        //$this->dropIndex('IND-email_e_communication_id', '{{%email}}');
        //$this->dropIndex('ind-lead_flow_status_lf_from_status_id', '{{%lead_flow}}');

        $this->createIndex('IND-setting_s_updated_dt', '{{%setting}}', ['s_updated_dt']);

        $this->createIndex('IND-uid_quotes', '{{%quotes}}', ['uid']);
        $this->createIndex('IND-leads_uid_source_id', '{{%leads}}', ['uid', 'source_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createIndex('IND-call_c_from', '{{%call}}', ['c_from']);
        $this->createIndex('IND-call_c_to', '{{%call}}', ['c_to']);
        //$this->createIndex('IND-email_e_communication_id', '{{%email}}', ['c_to']);
        //$this->createIndex('ind-lead_flow_status_lf_from_status_id', '{{%lead_flow}}', ['status', 'lf_from_status_id']);

        $this->dropIndex('IND-setting_s_updated_dt', '{{%setting}}');

        $this->dropIndex('IND-uid_quotes', '{{%quotes}}');
        $this->dropIndex('IND-leads_uid_source_id', '{{%leads}}');
    }


}
