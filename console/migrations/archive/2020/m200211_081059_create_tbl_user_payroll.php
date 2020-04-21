<?php

use yii\db\Migration;

/**
 * Class m200211_081059_create_tbl_user_payroll
 */
class m200211_081059_create_tbl_user_payroll extends Migration
{
	/**
	 * @return bool|void
	 * @throws \yii\base\NotSupportedException
	 */
    public function safeUp()
    {
    	$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

    	$this->createTable('{{%user_payroll}}', [
    		'ups_id' => $this->primaryKey(),
			'ups_user_id' => $this->integer()->notNull(),
			'ups_month' => $this->tinyInteger()->notNull(),
			'ups_year' => $this->smallInteger()->notNull(),
			'ups_base_amount' => $this->decimal(8, 2),
			'ups_profit_amount' => $this->decimal(8, 2),
			'ups_tax_amount' => $this->decimal(8,2),
			'ups_payment_amount' => $this->decimal(8,2 ),
			'ups_total_amount' => $this->decimal(8, 2),
			'ups_agent_status_id' => $this->tinyInteger(1),
			'ups_status_id' => $this->tinyInteger(1),
			'ups_created_dt' => $this->dateTime(),
			'ups_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->createIndex('unique-user_payroll-ups_user_id-ups_month-ups_year', '{{%user_payroll}}', ['ups_user_id', 'ups_month', 'ups_year'], true);

		$this->addForeignKey(
			'fk-user_payroll-ups_user_id',
			'{{%user_payroll}}',
			'ups_user_id',
			'{{%employees}}',
			'id',
			'CASCADE',
			'CASCADE'
		);

		\Yii::$app->db->getSchema()->refreshTableSchema('{{%user_payroll}}');

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
	}

	/**
	 * @return bool|void
	 * @throws \yii\base\NotSupportedException
	 */
    public function safeDown()
    {
    	$this->dropForeignKey('fk-user_payroll-ups_user_id', '{{%user_payroll}}');
    	$this->dropIndex('unique-user_payroll-ups_user_id-ups_month-ups_year', '{{%user_payroll}}');
    	$this->dropTable('{{%user_payroll}}');

		\Yii::$app->db->getSchema()->refreshTableSchema('{{%user_payroll}}');

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
    }
}
