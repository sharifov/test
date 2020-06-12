<?php

use yii\db\Migration;

/**
 * Class m200529_065504_add_column_st_airline_waiver_tbl_sale_ticket
 */
class m200529_065504_add_column_st_airline_waiver_tbl_sale_ticket extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->truncateTable('{{%sale_ticket}}');

		$this->addColumn('{{%sale_ticket}}', 'st_refund_waiver', $this->string(50));
		$this->alterColumn('{{%sale_ticket}}', 'st_penalty_amount', $this->decimal(8, 2));

		$this->createIndex('INDEX-st_departure_dt', '{{%case_sale}}', ['css_departure_dt']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropColumn('{{%sale_ticket}}', 'st_refund_waiver');
		$this->alterColumn('{{%sale_ticket}}', 'st_penalty_amount', $this->string(50));

		$this->dropIndex('INDEX-st_departure_dt', '{{%case_sale}}');
	}
}
