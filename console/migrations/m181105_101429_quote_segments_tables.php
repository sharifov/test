<?php
use yii\db\Migration;

/**
 * Class m181105_101429_quote_segments_tables
 */
class m181105_101429_quote_segments_tables extends Migration
{

    /**
     *
     * {@inheritdoc}
     *
     */
    public function safeUp()
    {
        $this->addColumn('{{%quotes}}', 'last_ticket_date', $this->dateTime());
        $this->alterColumn('{{%quotes}}', 'gds', $this->string(1));
        $this->alterColumn('{{%quotes}}', 'cabin', $this->string(20));
        $this->alterColumn('{{%quotes}}', 'trip_type', $this->string(2));
        $this->alterColumn('{{%quotes}}', 'main_airline_code', $this->string(2));
        $this->alterColumn('{{%quotes}}', 'fare_type', $this->string(20));
        $this->alterColumn('{{%quotes}}', 'pcc', $this->string(20));

        $this->createTable('{{%quote_trip}}', [
            'qt_id' => $this->primaryKey(),
            'qt_duration' => $this->integer(),
            'qt_key' => $this->string(255),
            'qt_quote_id' => $this->integer()
        ]);
        $this->addForeignKey('fk_quote_trip_quotes', '{{%quote_trip}}', 'qt_quote_id', 'quotes', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%quote_segment}}', [
            'qs_id' => $this->primaryKey(),
            'qs_departure_time' => $this->dateTime(),
            'qs_arrival_time' => $this->dateTime(),
            'qs_stop' => $this->integer(1)
                ->null(),
            'qs_flight_number' => $this->string(5),
            'qs_booking_class' => $this->string(1),
            'qs_duration' => $this->integer(6),
            'qs_departure_airport_code' => $this->string(3),
            'qs_departure_airport_terminal' => $this->string(5),
            'qs_arrival_airport_code' => $this->string(3),
            'qs_arrival_airport_terminal' => $this->string(5),
            'qs_operating_airline' => $this->string(2),
            'qs_marketing_airline' => $this->string(2),
            'qs_air_equip_type' => $this->string(3),
            'qs_marriage_group' => $this->string(2),
            'qs_mileage' => $this->integer(),
            'qs_cabin' => $this->string(1),
            'qs_meal' => $this->string(3),
            'qs_fare_code' => $this->string(15),
            'qs_trip_id' => $this->integer(),
            'qs_key' => $this->string(255),
            'qs_created_dt' => $this->dateTime()
                ->defaultExpression('NOW()'),
            'qs_updated_dt' => $this->dateTime(),
            'qs_updated_user_id' => $this->integer()
        ]);
        $this->addForeignKey('fk_quote_segment_trip', '{{%quote_segment}}', 'qs_trip_id', 'quote_trip', 'qt_id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_updated_user', '{{%quote_segment}}', 'qs_updated_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%quote_segment_baggage}}', [
            'qsb_id' => $this->primaryKey(),
            'qsb_pax_code' => $this->string(3),
            'qsb_segment_id' => $this->integer(),
            'qsb_airline_code' => $this->string(3),
            'qsb_allow_pieces' => $this->integer(1),
            'qsb_allow_weight' => $this->integer(2),
            'qsb_allow_unit' => $this->string(4),
            'qsb_allow_max_weight' => $this->string(100),
            'qsb_allow_max_size' => $this->string(100),
            'qsb_created_dt' => $this->dateTime()
                ->defaultExpression('NOW()'),
            'qsb_updated_dt' => $this->dateTime(),
            'qsb_updated_user_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_segment_baggage_updated_user', '{{%quote_segment_baggage}}', 'qsb_updated_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_quote_segment_baggage', '{{%quote_segment_baggage}}', 'qsb_segment_id', 'quote_segment', 'qs_id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%quote_segment_stop}}', [
            'qss_id' => $this->primaryKey(),
            'qss_location_code' => $this->string(3),
            'qss_departure_dt' => $this->dateTime(),
            'qss_arrival_dt' => $this->dateTime(),
            'qss_duration' => $this->integer(),
            'qss_elapsed_time' => $this->integer(),
            'qss_equipment' => $this->string(5),
            'qss_segment_id' => $this->integer()
        ]);

        $this->addForeignKey('fk_quote_segment_stops', '{{%quote_segment_stop}}', 'qss_segment_id', 'quote_segment', 'qs_id', 'CASCADE', 'CASCADE');
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_quote_segment_stops', '{{%quote_segment_stop}}');
        $this->dropTable('{{%quote_segment_stop}}');

        $this->dropForeignKey('fk_quote_segment_baggage', '{{%quote_segment_baggage}}');
        $this->dropForeignKey('fk_segment_baggage_updated_user', '{{%quote_segment_baggage}}');
        $this->dropTable('{{%quote_segment_baggage}}');

        $this->dropForeignKey('fk_updated_user', '{{%quote_segment}}');
        $this->dropForeignKey('fk_quote_segment_trip', '{{%quote_segment}}');
        $this->dropTable('{{%quote_segment}}');

        $this->dropForeignKey('fk_quote_trip_quotes', '{{%quote_trip}}');
        $this->dropTable('{{%quote_trip}}');

        $this->dropColumn('{{%quotes}}', 'last_ticket_date');
    }
}
