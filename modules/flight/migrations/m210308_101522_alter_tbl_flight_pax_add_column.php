<?php

namespace modules\flight\migrations;

use modules\flight\models\FlightPax;
use yii\db\Migration;
use yii\db\Query;

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

        $flightPax = FlightPax::find()->select(['fp_id'])->asArray()->all();
        $fp = new FlightPax();
        foreach ($flightPax as $pax) {
            $query = (new Query())->createCommand()->update(FlightPax::tableName(), ['fp_uid' => $fp->generateUid()], ['fp_id' => $pax['fp_id']]);
            $query->execute();
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
