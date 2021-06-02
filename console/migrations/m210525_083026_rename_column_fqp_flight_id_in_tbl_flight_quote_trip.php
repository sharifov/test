<?php

use yii\db\Migration;

/**
 * Class m210525_083026_rename_column_fqp_flight_id_in_tbl_flight_quote_trip
 */
class m210525_083026_rename_column_fqp_flight_id_in_tbl_flight_quote_trip extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%flight_quote_trip}}', 'fqp_flight_id', 'fqt_flight_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%flight_quote_trip}}', 'fqt_flight_id', 'fqp_flight_id');
    }
}
