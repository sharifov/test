<?php

namespace modules\attraction\migrations;

use yii\db\Migration;

/**
 * Class m210312_093032_add_column_atnq_availability_id_attraction_quote_tbl
 */
class m210312_093032_add_column_atnq_availability_id_attraction_quote_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%attraction_quote}}', 'atnq_availability_id', $this->string(40));
        $this->addColumn('{{%attraction_quote}}', 'atnq_availability_product_id', $this->string(40));
        $this->addColumn('{{%attraction_quote}}', 'atnq_availability_date', $this->date());
        $this->addColumn('{{%attraction_quote}}', 'atnq_availability_is_valid', $this->tinyInteger()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%attraction_quote}}', 'atnq_availability_id');
        $this->dropColumn('{{%attraction_quote}}', 'atnq_availability_product_id');
        $this->dropColumn('{{%attraction_quote}}', 'atnq_availability_date');
        $this->dropColumn('{{%attraction_quote}}', 'atnq_availability_is_valid');
    }
}
