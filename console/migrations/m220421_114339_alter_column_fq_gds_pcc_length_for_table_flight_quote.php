<?php

use yii\db\Migration;

/**
 * Class m220421_114339_alter_column_fq_gds_pcc_length_for_table_flight_quote
 */
class m220421_114339_alter_column_fq_gds_pcc_length_for_table_flight_quote extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%flight_quote}}', '[[fq_gds_pcc]]', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%flight_quote}}', '[[fq_gds_pcc]]', $this->string(10));
    }
}
