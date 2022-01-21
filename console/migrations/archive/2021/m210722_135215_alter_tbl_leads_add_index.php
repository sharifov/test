<?php

use yii\db\Migration;

/**
 * Class m210722_135215_alter_tbl_leads_add_index
 */
class m210722_135215_alter_tbl_leads_add_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-leads-bo_flight_id', '{{%leads}}', 'bo_flight_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-leads-bo_flight_id', '{{%leads}}');
    }
}
