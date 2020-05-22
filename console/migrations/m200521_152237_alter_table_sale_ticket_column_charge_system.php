<?php

use yii\db\Migration;

/**
 * Class m200521_152237_alter_table_sale_ticket_column_charge_system
 */
class m200521_152237_alter_table_sale_ticket_column_charge_system extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->alterColumn('{{%sale_ticket}}', 'st_charge_system', $this->string(50));

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%sale_ticket}}');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->alterColumn('{{%sale_ticket}}', 'st_charge_system', $this->tinyInteger(2));

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

		Yii::$app->db->getSchema()->refreshTableSchema('{{%sale_ticket}}');
	}
}
