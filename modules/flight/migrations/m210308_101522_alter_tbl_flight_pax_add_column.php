<?php

namespace modules\flight\migrations;

use modules\flight\models\FlightPax;
use yii\db\Migration;

/**
 * Class m210308_101522_alter_tbl_flight_pax_add_column
 */
class m210308_101522_alter_tbl_flight_pax_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%flight_pax}}', 'fp_uid', $this->string(15)->unique()->after('fp_flight_id'));

        $flightPax = FlightPax::find()->all();
        foreach ($flightPax as $pax) {
            $pax->fp_uid = $pax->generateUid();
            $pax->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%flight_pax}}', 'fp_uid');
    }
}
