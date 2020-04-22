<?php

use yii\db\Migration;

/**
 * Class m180816_121514_add_column_discount_id_on_leads_table
 */
class m180816_121514_add_column_discount_id_on_leads_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'discount_id', $this->string());
        $this->addColumn('{{%leads}}', 'bo_flight_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'discount_id');
        $this->dropColumn('{{%leads}}', 'bo_flight_id');
    }
}
