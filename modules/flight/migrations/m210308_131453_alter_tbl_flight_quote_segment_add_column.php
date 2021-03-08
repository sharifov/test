<?php

namespace modules\flight\migrations;

use modules\flight\models\FlightQuoteSegment;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m210308_131453_alter_tbl_flight_quote_segment_add_column
 */
class m210308_131453_alter_tbl_flight_quote_segment_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight_quote_segment}}', 'fqs_uid', $this->string(20)->unique()->after('fqs_flight_quote_trip_id'));
        $fqs = FlightQuoteSegment::find()->all();
        foreach ($fqs as $flightQuoteSegment) {
            $flightQuoteSegment->fqs_uid = $flightQuoteSegment->generateUid();
            $flightQuoteSegment->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flight_quote_segment}}', 'fqs_uid');
    }
}
