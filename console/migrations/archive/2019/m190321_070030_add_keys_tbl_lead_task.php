<?php

use yii\db\Migration;

/**
 * Class m190321_070030_add_keys_tbl_lead_task
 */
class m190321_070030_add_keys_tbl_lead_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND_lead_task', '{{%lead_task}}', ['lt_user_id', 'lt_completed_dt', 'lt_date']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropIndex('IND_lead_task', '{{%lead_task}}');
    }

}
