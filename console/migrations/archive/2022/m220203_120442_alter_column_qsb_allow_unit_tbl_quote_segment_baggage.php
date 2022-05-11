<?php

use yii\db\Migration;

/**
 * Class m220203_120442_alter_column_qsb_allow_unit_tbl_quote_segment_baggage
 */
class m220203_120442_alter_column_qsb_allow_unit_tbl_quote_segment_baggage extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%quote_segment_baggage}}', 'qsb_allow_unit', $this->string(20));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%quote_segment_baggage}}', 'qsb_allow_unit', $this->string(4));
    }
}
