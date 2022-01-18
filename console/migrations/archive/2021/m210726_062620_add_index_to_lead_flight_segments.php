<?php

use yii\db\Migration;

/**
 * Class m210726_062620_add_index_to_lead_flight_segments
 */
class m210726_062620_add_index_to_lead_flight_segments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-lead_flight_segments-destination', 'lead_flight_segments', 'destination');
        $this->createIndex('IND-lead_flight_segments-origin', 'lead_flight_segments', 'origin');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-lead_flight_segments-destination', 'lead_flight_segments');
        $this->dropIndex('IND-lead_flight_segments-origin', 'lead_flight_segments');
    }
}
