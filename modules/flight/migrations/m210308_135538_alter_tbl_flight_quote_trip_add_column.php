<?php

namespace modules\flight\migrations;

use modules\flight\models\FlightQuoteTrip;
use yii\db\Migration;

/**
 * Class m210308_135538_alter_tbl_flight_quote_trip_add_column
 */
class m210308_135538_alter_tbl_flight_quote_trip_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight_quote_trip}}', 'fqt_uid', $this->string(20)->unique()->after('fqt_id'));
        $fqt = FlightQuoteTrip::find()->all();
        foreach ($fqt as $flightQuoteTrip) {
            $flightQuoteTrip->fqt_uid = $flightQuoteTrip->generateUid();
            $flightQuoteTrip->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flight_quote_trip}}', 'fqt_uid');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210308_135538_alter_tbl_flight_quote_trip_add_column cannot be reverted.\n";

        return false;
    }
    */
}
