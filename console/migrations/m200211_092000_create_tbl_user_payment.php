<?php

use yii\db\Migration;

/**
 * Class m200211_092000_create_tbl_user_payment
 */
class m200211_092000_create_tbl_user_payment extends Migration
{
    public function safeUp()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%user_payment}}', [
			'upt_id' => $this->primaryKey(),
			'upt_assigned_user_id' => $this->integer()->notNull(),
			'upt_category_id' => $this->integer()->notNull(),
			'upt_status_id' => $this->tinyInteger(1),
			'upt_amount' => $this->decimal(8, 2),
			'upt_description' => $this->string(),
			'upt_date' => $this->date(),
			'upt_created_user_id' => $this->integer(),
			'upt_updated_user_id' => $this->integer(),
			'upt_created_dt' => $this->dateTime(),
			'upt_updated_dt' => $this->dateTime(),
			'upt_payroll_id' => $this->integer()
		], $tableOptions);

		$this->addForeignKey(
			'fk-user_payment-upt_assigned_user_id',
			'{{%user_payment}}',
			'upt_assigned_user_id',
			'{{%employees}}',
			'id',
			'CASCADE'
		);

		$this->addForeignKey(
			'fk-user_payment-upt_category_id',
			'{{%user_payment}}',
			'upt_category_id',
			'{{%user_payment_category}}',
			'upc_id'
		);

		$this->addForeignKey(
			'fk-user_payment-upt_created_user_id',
			'{{%user_payment}}',
			'upt_created_user_id',
			'{{%employees}}',
			'id'
		);

		$this->addForeignKey(
			'fk-user_payment-upt_updated_user_id',
			'{{%user_payment}}',
			'upt_updated_user_id',
			'{{%employees}}',
			'id'
		);

		$this->addForeignKey(
			'fk-user_payment-upt_payroll_id',
			'{{%user_payment}}',
			'upt_payroll_id',
			'{{%user_payroll}}',
			'ups_id'
		);

		\Yii::$app->db->getSchema()->refreshTableSchema('{{%user_payment}}');

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
	}

    public function safeDown()
    {
    	$this->dropForeignKey('fk-user_payment-upt_assigned_user_id', '{{%user_payment}}');
    	$this->dropForeignKey('fk-user_payment-upt_category_id', '{{%user_payment}}');
    	$this->dropForeignKey('fk-user_payment-upt_created_user_id', '{{%user_payment}}');
    	$this->dropForeignKey('fk-user_payment-upt_updated_user_id', '{{%user_payment}}');
    	$this->dropForeignKey('fk-user_payment-upt_payroll_id', '{{%user_payment}}');

    	$this->dropTable('{{%user_payment}}');

		\Yii::$app->db->getSchema()->refreshTableSchema('{{%user_payment}}');

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
    }
}
