<?php

use yii\db\Migration;

/**
 * Class m190516_151008_create_indexes2
 */
class m190516_151008_create_indexes2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-api_log_al_action', '{{%api_log}}', ['al_action']);
        $this->createIndex('IND-lead_task_lt_date', '{{%lead_task}}', ['lt_date']);
        $this->createIndex('IND-leads_request_ip', '{{%leads}}', ['request_ip']);



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-api_log_al_action', '{{%api_log}}');
        $this->dropIndex('IND-lead_task_lt_date', '{{%lead_task}}');
        $this->dropIndex('IND-leads_request_ip', '{{%leads}}');
    }

}
