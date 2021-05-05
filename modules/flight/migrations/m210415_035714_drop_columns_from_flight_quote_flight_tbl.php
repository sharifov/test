<?php

namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m210415_035714_drop_columns_from_flight_quote_flight_tbl
 */
class m210415_035714_drop_columns_from_flight_quote_flight_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%flight_quote_flight}}', 'fqf_type_id');
        $this->dropColumn('{{%flight_quote_flight}}', 'fqf_cabin_class');
        $this->dropColumn('{{%flight_quote_flight}}', 'fqf_fare_type_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%flight_quote_flight}}', 'fqf_type_id', $this->tinyInteger());
        $this->addColumn('{{%flight_quote_flight}}', 'fqf_cabin_class', $this->string(1));
        $this->addColumn('{{%flight_quote_flight}}', 'fqf_fare_type_id', $this->tinyInteger());
    }
}
