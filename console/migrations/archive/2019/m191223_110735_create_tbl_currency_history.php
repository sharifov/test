<?php

use yii\db\Migration;

/**
 * Class m191223_110735_create_tbl_currency_history
 */
class m191223_110735_create_tbl_currency_history extends Migration
{
	public $routes = [
		'/currency-history/*',
	];

	public $roles = [
		\common\models\Employee::ROLE_ADMIN,
		\common\models\Employee::ROLE_SUPER_ADMIN,
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

		$this->createTable('{{%currency_history}}',	[
			'cur_his_code'				=> $this->string(3)->notNull(),
			'cur_his_base_rate'     	=> $this->decimal(8, 5)->defaultValue(1),
			'cur_his_app_rate'    		=> $this->decimal(8, 5)->defaultValue(1),
			'cur_his_app_percent'  		=> $this->decimal(5, 3)->defaultValue(0),
			'cur_his_created' 			=> $this->date(),
			'cur_his_main_created_dt'   => $this->dateTime(),
			'cur_his_main_updated_dt'   => $this->dateTime(),
			'cur_his_main_synch_dt'     => $this->dateTime()
		], $tableOptions);

		$this->addPrimaryKey('PK-currency-cur_code', '{{%currency_history}}', ['cur_his_code', 'cur_his_created']);

		(new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%currency_history}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropTable('{{%currency_history}}');

		(new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);

		Yii::$app->db->getSchema()->refreshTableSchema('{{%currency_history}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
