<?php

use yii\db\Migration;

/**
 * Class m220120_055908_correction_booking_id_in_flight_quote_flight
 */
class m220120_055908_correction_booking_id_in_flight_quote_flight extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220120_055908_correction_booking_id_in_flight_quote_flight cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220120_055908_correction_booking_id_in_flight_quote_flight cannot be reverted.\n";

        return false;
    }
    */
}
