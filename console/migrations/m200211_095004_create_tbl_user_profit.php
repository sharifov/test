<?php

use yii\db\Migration;

/**
 * Class m200211_095004_create_tbl_user_profit
 */
class m200211_095004_create_tbl_user_profit extends Migration
{
    public function safeUp()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%user_profit}}', [
			'up_id' => $this->primaryKey(),
			'up_user_id' => $this->integer()->notNull(),
			'up_lead_id' => $this->integer(),
			'up_order_id' => $this->integer(),
			'up_product_quote_id' => $this->integer(),
			'up_percent' => $this->smallInteger(),
			'up_profit' => $this->decimal(8, 2),
			'up_split_percent' => $this->smallInteger(),
			'up_amount' => $this->decimal(8,2),
			'up_status_id' => $this->tinyInteger(1),
			'up_created_dt' => $this->dateTime(),
			'up_updated_dt' => $this->dateTime(),
			'up_payroll_id' => $this->integer(),
			'up_type_id' => $this->tinyInteger(2)
		], $tableOptions);

		$this->addForeignKey(
			'fk-user_profit-up_user_id',
			'{{%user_profit}}',
			'up_user_id',
			'{{%employees}}',
			'id',
			'CASCADE'
		);

		$this->addForeignKey(
			'fk-user_profit-up_lead_id',
			'{{%user_profit}}',
			'up_lead_id',
			'{{%leads}}',
			'id'
		);

		$this->addForeignKey(
			'fk-user_profit-up_order_id',
			'{{%user_profit}}',
			'up_order_id',
			'{{%order}}',
			'or_id'
		);

		$this->addForeignKey(
			'fk-user_profit-up_product_quote_id',
			'{{%user_profit}}',
			'up_product_quote_id',
			'{{%product_quote}}',
			'pq_id'
		);

		$this->addForeignKey(
			'fk-user_profit-up_payroll_id',
			'{{%user_profit}}',
			'up_payroll_id',
			'{{%user_payroll}}',
			'ups_id'
		);

		\Yii::$app->db->getSchema()->refreshTableSchema('{{%user_profit}}');

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
	}

    public function safeDown()
    {
		$this->dropForeignKey('fk-user_profit-up_user_id', '{{%user_profit}}');
		$this->dropForeignKey('fk-user_profit-up_lead_id', '{{%user_profit}}');
		$this->dropForeignKey('fk-user_profit-up_order_id', '{{%user_profit}}');
		$this->dropForeignKey('fk-user_profit-up_product_quote_id', '{{%user_profit}}');
		$this->dropForeignKey('fk-user_profit-up_payroll_id', '{{%user_profit}}');

		$this->dropTable('{{%user_profit}}');

		\Yii::$app->db->getSchema()->refreshTableSchema('{{%user_profit}}');

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
	}
}
