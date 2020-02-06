<?php
namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m200130_132402_alter_tbl_flight_quote_pax_price_add_column_cnt
 */
class m200130_132402_alter_tbl_flight_quote_pax_price_add_column_cnt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%flight_quote_pax_price}}', 'qpp_cnt', $this->tinyInteger(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropColumn('{{%flight_quote_pax_price}}', 'qpp_cnt');
    }
}
