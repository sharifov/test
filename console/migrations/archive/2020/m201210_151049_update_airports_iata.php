<?php

use yii\db\Migration;

/**
 * Class m201210_151049_update_airports_iata
 */
class m201210_151049_update_airports_iata extends Migration
{
    public array $airportsUpdate = [
        "QDU" => "DUS",     // Duesseldorf
        "QKL" => "CGN",     // Cologne
        "QRH" => "RTM",     // Rotterdam
        "TSE" => "NQZ",     // Nursultan Nazarbayev
        "XWG" => "SXB",     // Strasbourg
        "ZFJ" => "RNS",     // Rennes
        "ZFQ" => "BOD",     // Bordeaux
    ];

    public array $airportsDelete = [
        "ZTI" => "ZTI",
        "ZYK" => "ZYK",
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach ($this->airportsUpdate as $oldIata => $newIata) {
            $this->update(
                '{{%quote_segment}}',
                ['qs_arrival_airport_code' => $newIata],
                ['qs_arrival_airport_code' => $oldIata]
            );

            $this->update(
                '{{%quote_segment}}',
                ['qs_departure_airport_code' => $newIata],
                ['qs_departure_airport_code' => $oldIata]
            );

            $this->update(
                '{{%quote_segment_stop}}',
                ['qss_location_code' => $newIata],
                ['qss_location_code' => $oldIata]
            );
        }

        foreach ($this->airportsDelete as $iata) {
            $this->delete(
                '{{%quote_segment}}',
                ['qs_arrival_airport_code' => $iata]
            );

            $this->delete(
                '{{%quote_segment}}',
                ['qs_departure_airport_code' => $iata],
            );

            $this->delete(
                '{{%quote_segment_stop}}',
                ['qss_location_code' => $iata],
            );
        }

        $this->delete(
            '{{%airports}}',
            'a_updated_dt IS NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
