<?php

use yii\db\Migration;

/**
 * Class m211019_191105_add_index_lead_flow
 */
class m211019_191105_add_index_lead_flow extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'IND-lead_flow-user_stat_report',
            'lead_flow',
            ['status', 'created']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-lead_flow-processed_report', 'lead_flow');
    }
}
