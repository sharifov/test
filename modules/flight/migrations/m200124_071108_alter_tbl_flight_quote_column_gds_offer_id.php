<?php
namespace modules\flight\migrations;

use yii\db\Migration;

/**
 * Class m200124_071108_alter_tbl_flight_quote_column_gds_offer_id
 */
class m200124_071108_alter_tbl_flight_quote_column_gds_offer_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('{{%flight_quote}}', 'fq_gds_offer_id', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->alterColumn('{{%flight_quote}}', 'fq_gds_offer_id', $this->tinyInteger(1));
    }
}
