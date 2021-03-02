<?php

use yii\db\Migration;

/**
 * Class m210302_100035_add_columns_tbl_hotel_quote
 */
class m210302_100035_add_columns_tbl_hotel_quote extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%hotel_quote}}', 'hq_check_in_date', $this->date());
        $this->addColumn('{{%hotel_quote}}', 'hq_check_out_date', $this->date());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%hotel_quote}}', 'hq_check_in_date');
        $this->dropColumn('{{%hotel_quote}}', 'hq_check_out_date');
    }
}
