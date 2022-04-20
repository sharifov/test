<?php

use yii\db\Migration;

/**
 * Class m220117_143640_add_columns_child_booking_id
 */
class m220117_143640_add_columns_child_booking_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('flight_quote_flight', 'fqf_child_booking_id', $this->string(50));
        $this->addColumn('flight_quote_booking', 'fqb_child_booking_id', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('flight_quote_flight', 'fqf_child_booking_id');
        $this->dropColumn('flight_quote_booking', 'fqb_child_booking_id');
    }
}
