<?php

use modules\flight\models\FlightQuoteFlight;
use yii\db\Migration;
use yii\helpers\Console;

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
        $query = FlightQuoteFlight::find()
            ->where(['fqf_booking_id' => null])
            ->andWhere(['IS NOT', 'fqf_child_booking_id', null])
            ->andWhere(['>=', 'DATE(fqf_created_dt)', '2022-01-19'])
            ->all();

        echo PHP_EOL;
        $total = count($query);
        $processed = 0;
        $result = [];
        Console::startProgress($processed, $total);

        foreach ($query as $flightQuoteFlight) {
            $flightQuoteFlight->fqf_booking_id = $flightQuoteFlight->fqf_child_booking_id;
            $flightQuoteFlight->save();

            $result[$flightQuoteFlight->fqf_id]['fqf_child_booking_id'] = $flightQuoteFlight->fqf_child_booking_id;

            $processed++;
            Console::updateProgress($processed, $total);
        }

        \Yii::info($result, 'info\Result:correction_booking_id_in_flight_quote_flight');

        Console::endProgress(false);
        echo PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220120_055908_correction_booking_id_in_flight_quote_flight cannot be reverted.\n";
    }
}
