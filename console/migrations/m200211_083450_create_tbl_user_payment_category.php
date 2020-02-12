<?php

use sales\model\user\paymentCategory\UserPaymentCategory;
use yii\db\Migration;

/**
 * Class m200211_083450_create_tbl_user_payment_category
 */
class m200211_083450_create_tbl_user_payment_category extends Migration
{

	public $categories = [
		'Bonus',
		'Penalty',
		'Other'
	];

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

		$this->createTable('{{%user_payment_category}}', [
			'upc_id' => $this->primaryKey(),
			'upc_name' => $this->string(30),
			'upc_description' => $this->string(),
			'upc_enabled' => $this->boolean(),
			'upc_created_user_id' => $this->integer(),
			'upc_updated_user_id' => $this->integer(),
			'upc_created_dt' => $this->dateTime(),
			'upc_updated_dt' => $this->dateTime()
		], $tableOptions);

    	$this->addForeignKey(
    		'fk-user_payment_category-upc_created_user_id',
			'{{%user_payment_category}}',
			'upc_created_user_id',
			'{{%employees}}',
			'id'
			);

		$this->addForeignKey(
			'fk-user_payment_category-upc_updated_user_id',
			'{{%user_payment_category}}',
			'upc_updated_user_id',
			'{{%employees}}',
			'id'
		);

		\Yii::$app->db->getSchema()->refreshTableSchema('{{%user_payment_category}}');


		foreach ($this->categories as $category) {
			$newCategory = new UserPaymentCategory();
			$newCategory->upc_name = $category;
			$newCategory->upc_enabled = 1;
			$newCategory->save();
		}

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
    	$this->dropForeignKey('fk-user_payment_category-upc_created_user_id', '{{%user_payment_category}}');
    	$this->dropForeignKey('fk-user_payment_category-upc_updated_user_id', '{{%user_payment_category}}');
    	$this->dropTable('{{%user_payment_category}}');

		\Yii::$app->db->getSchema()->refreshTableSchema('{{%user_payment_category}}');

		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
    }
}
