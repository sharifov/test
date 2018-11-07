<?php

use yii\db\Migration;

/**
 * Class m181107_081334_airports_alter_add_fk
 */
class m181107_081334_airports_alter_add_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /* $this->addForeignKey('fk-lead_flight_segment_origin', '{{%lead_flight_segments}}', 'origin', '{{%airports}}', 'iata');
        $this->addForeignKey('fk-lead_flight_segment_destination', '{{%lead_flight_segments}}', 'destination', '{{%airports}}', 'iata'); */
        $this->addForeignKey('fk-quote_segment_departure', '{{%quote_segment}}', 'qs_departure_airport_code', '{{%airports}}', 'iata');
        $this->addForeignKey('fk-quote_segment_arrival', '{{%quote_segment}}', 'qs_arrival_airport_code', '{{%airports}}', 'iata');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-quote_segment_arrival', '{{%quote_segment}}');
        $this->dropForeignKey('fk-quote_segment_departure', '{{%quote_segment}}');
        $this->dropIndex('fk-quote_segment_arrival', '{{%quote_segment}}');
        $this->dropIndex('fk-quote_segment_departure', '{{%quote_segment}}');
        /* $this->dropForeignKey('fk-lead_flight_segment_origin', '{{%lead_flight_segments}}');
        $this->dropForeignKey('fk-lead_flight_segment_destination', '{{%lead_flight_segments}}'); */
    }
}
