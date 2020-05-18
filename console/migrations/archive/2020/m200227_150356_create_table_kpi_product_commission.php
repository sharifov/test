<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200227_150356_create_table_kpi_product_commission
 */
class m200227_150356_create_table_kpi_product_commission extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/kpi-product-commission-crud/index',
		'/kpi-product-commission-crud/create',
		'/kpi-product-commission-crud/update',
		'/kpi-product-commission-crud/delete',
		'/kpi-product-commission-crud/view',
	];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%kpi_product_commission}}', [
			'pc_product_type_id' => $this->integer()->notNull(),
			'pc_performance' => $this->integer()->notNull(),
			'pc_commission_percent' => $this->tinyInteger(3)->notNull(),
			'pc_created_user_id' => $this->integer(),
			'pc_updated_user_id' => $this->integer(),
			'pc_created_dt' => $this->dateTime(),
			'pc_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addPrimaryKey('PK-kpi_product_commission-product-performance-commission', '{{%kpi_product_commission}}', ['pc_product_type_id', 'pc_performance', 'pc_commission_percent']);

		$this->addForeignKey('FK-kpi_product_commission-pc_product_type_id', '{{%kpi_product_commission}}', 'pc_product_type_id', '{{%product_type}}', 'pt_id', 'CASCADE', 'CASCADE');

		$this->addForeignKey('FK-kpi_product_commission-pc_created_user_id', '{{%kpi_product_commission}}', 'pc_created_user_id', '{{%employees}}', 'id');

		$this->addForeignKey('FK-kpi_product_commission-pc_updated_user_id', '{{%kpi_product_commission}}', 'pc_updated_user_id', '{{%employees}}', 'id');

		(new RbacMigrationService())->up($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%kpi_product_commission}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropForeignKey('FK-kpi_product_commission-pc_product_type_id', '{{%kpi_product_commission}}');
		$this->dropForeignKey('FK-kpi_product_commission-pc_created_user_id', '{{%kpi_product_commission}}');
		$this->dropForeignKey('FK-kpi_product_commission-pc_updated_user_id', '{{%kpi_product_commission}}');

		$this->dropTable('{{%kpi_product_commission}}');

		(new RbacMigrationService())->up($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%kpi_product_commission}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
