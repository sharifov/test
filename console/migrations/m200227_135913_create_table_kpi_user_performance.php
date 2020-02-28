<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200227_135913_create_table_kpi_user_performance
 */
class m200227_135913_create_table_kpi_user_performance extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/kpi-user-performance-crud/index',
		'/kpi-user-performance-crud/create',
		'/kpi-user-performance-crud/update',
		'/kpi-user-performance-crud/delete',
		'/kpi-user-performance-crud/view',
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

		$this->createTable('{{%kpi_user_performance}}', [
			'up_user_id' => $this->integer()->notNull(),
			'up_year' => $this->smallInteger(4)->notNull(),
			'up_month' => $this->tinyInteger(2)->notNull(),
			'up_performance' => $this->smallInteger(),
			'up_created_user_id' => $this->integer(),
			'up_updated_user_id' => $this->integer(),
			'up_created_dt' => $this->dateTime(),
			'up_updated_dt' => $this->dateTime(),
		], $tableOptions);

		$this->addPrimaryKey('PK-kpi_user_performance-up_user_id-up_year-up_month', '{{%kpi_user_performance}}', ['up_user_id', 'up_year', 'up_month']);

		$this->addForeignKey('FK-kpi_user_performance-up_user_id', '{{%kpi_user_performance}}', 'up_user_id', '{{%employees}}', 'id', 'CASCADE', 'CASCADE');

		$this->addForeignKey('FK-kpi_user_performance-up_created_user_id', '{{%kpi_user_performance}}', 'up_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		$this->addForeignKey('FK-kpi_user_performance-up_updated_user_id', '{{%kpi_user_performance}}', 'up_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		(new RbacMigrationService())->up($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%kpi_user_performance}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('FK-kpi_user_performance-up_user_id', '{{%kpi_user_performance}}');
		$this->dropForeignKey('FK-kpi_user_performance-up_created_user_id', '{{%kpi_user_performance}}');
		$this->dropForeignKey('FK-kpi_user_performance-up_updated_user_id', '{{%kpi_user_performance}}');
    	$this->dropTable('{{%kpi_user_performance}}');

		(new RbacMigrationService())->down($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%kpi_user_performance}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

}
