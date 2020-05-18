<?php

use yii\db\Migration;

/**
 * Class m180802_112701_add_columns_on_lead_flight_segments_table
 */
class m180802_112701_add_columns_on_lead_flight_segments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%lead_flight_segments}}', 'flexibility', $this->integer(3));
        $this->addColumn('{{%lead_flight_segments}}', 'flexibility_type', $this->string(3));
        $this->addColumn('{{%lead_flight_segments}}', 'origin_label', $this->string());
        $this->addColumn('{{%lead_flight_segments}}', 'destination_label', $this->string());

        $this->addColumn('{{%client_email}}', 'comments', $this->text());

        $this->addColumn('{{%client_phone}}', 'comments', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%lead_flight_segments}}', 'flexibility');
        $this->dropColumn('{{%lead_flight_segments}}', 'flexibility_type');
        $this->dropColumn('{{%lead_flight_segments}}', 'origin_label');
        $this->dropColumn('{{%lead_flight_segments}}', 'destination_label');

        $this->dropColumn('{{%client_email}}', 'comments');

        $this->dropColumn('{{%client_phone}}', 'comments');
    }
}
