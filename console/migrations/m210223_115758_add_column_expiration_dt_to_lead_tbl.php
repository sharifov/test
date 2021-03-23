<?php

use yii\db\Migration;

/**
 * Class m210223_115758_add_column_expiration_dt_to_lead_tbl
 */
class m210223_115758_add_column_expiration_dt_to_lead_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'l_expiration_dt', $this->dateTime()->null());
        $this->createIndex('IND-leads-l_expiration_dt', '{{%leads}}', 'l_expiration_dt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'l_expiration_dt');
    }
}
