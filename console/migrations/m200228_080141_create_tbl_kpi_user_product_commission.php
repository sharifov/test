<?php
use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200228_080141_create_tbl_kpi_user_product_commission
 */
class m200228_080141_create_tbl_kpi_user_product_commission extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/kpi-user-product-commission-crud/index',
		'/kpi-user-product-commission-crud/create',
		'/kpi-user-product-commission-crud/update',
		'/kpi-user-product-commission-crud/delete',
		'/kpi-user-product-commission-crud/view',
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

		$this->createTable('{{%kpi_user_product_commission}}', [
			'upc_product_type_id' => $this->integer()->notNull(),
			'upc_user_id' => $this->integer()->notNull(),
			'upc_year' => $this->smallInteger(4)->notNull(),
			'upc_month' => $this->tinyInteger(2),
			'upc_performance' => $this->smallInteger(),
			'upc_commission_percent' => $this->smallInteger(),
			'upc_created_user_id' => $this->integer(),
			'upc_updated_user_id' => $this->integer(),
			'upc_created_dt' => $this->dateTime(),
			'upc_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addPrimaryKey('PK-kpi_user_product_commission', '{{%kpi_user_product_commission}}', ['upc_product_type_id', 'upc_user_id', 'upc_year', 'upc_month']);

		$this->addForeignKey('FK-kpi_user_product_commission-product_type_id', '{{%kpi_user_product_commission}}', 'upc_product_type_id', '{{%product_type}}', 'pt_id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-kpi_user_product_commission-upc_user_id', '{{%kpi_user_product_commission}}', 'upc_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-kpi_user_product_commission-upc_created_user_id', '{{%kpi_user_product_commission}}', 'upc_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-kpi_user_product_commission-upc_updated_user_id', '{{%kpi_user_product_commission}}', 'upc_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		(new RbacMigrationService())->up($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%kpi_user_product_commission}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('FK-kpi_user_product_commission-product_type_id', '{{%kpi_user_product_commission}}');
		$this->dropForeignKey('FK-kpi_user_product_commission-upc_user_id', '{{%kpi_user_product_commission}}');
		$this->dropForeignKey('FK-kpi_user_product_commission-upc_created_user_id', '{{%kpi_user_product_commission}}');
		$this->dropForeignKey('FK-kpi_user_product_commission-upc_updated_user_id', '{{%kpi_user_product_commission}}');

		$this->dropTable('{{%kpi_user_product_commission}}');

		(new RbacMigrationService())->down($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%kpi_user_product_commission}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
