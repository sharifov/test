<?php

use yii\db\Migration;

/**
 * Class m181025_070015_create_tbl_indexes
 */
class m181025_070015_create_tbl_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('api_log_index', '{{%api_log}}', ['al_user_id', 'al_request_dt']);
        $this->createIndex('lead_flow_index', '{{%lead_flow}}', ['employee_id', 'status', 'created']);
        $this->createIndex('log_index', '{{%log}}', ['log_time']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('api_log_index', '{{%api_log}}');
        $this->dropIndex('lead_flow_index', '{{%lead_flow}}');
        $this->dropIndex('log_index', '{{%log}}');
    }


}
