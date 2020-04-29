<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200326_153209_create_tbl_user_commission_rules
 */
class m200326_153209_create_tbl_user_commission_rules extends Migration
{
	public $route = [
		'/user-commission-rules-crud',
		'/user-commission-rules-crud/index',
		'/user-commission-rules-crud/create',
		'/user-commission-rules-crud/view',
		'/user-commission-rules-crud/update',
		'/user-commission-rules-crud/delete',
	];

	public $roles = [
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_ADMIN,
	];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%user_commission_rules}}', [
			'ucr_exp_month' => $this->smallInteger(),
			'ucr_kpi_percent' => $this->smallInteger(),
			'ucr_order_profit' => $this->integer(),
			'ucr_value' => $this->decimal(5,2),
			'ucr_created_user_id' => $this->integer(),
			'ucr_updated_user_id' => $this->integer(),
			'ucr_created_dt' => $this->dateTime(),
			'ucr_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addPrimaryKey('PK-user_commission_rules', '{{%user_commission_rules}}', [
			'ucr_exp_month', 'ucr_kpi_percent', 'ucr_order_profit'
		]);

		$this->addForeignKey('FK-user_commission_rules-created_user', '{{%user_commission_rules}}', 'ucr_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		$this->addForeignKey('FK-user_commission_rules-updated_user', '{{%user_commission_rules}}', 'ucr_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		(new RbacMigrationService())->up($this->route, $this->roles);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		(new RbacMigrationService())->down($this->route, $this->roles);

		$this->dropForeignKey('FK-user_commission_rules-created_user', '{{%user_commission_rules}}');
		$this->dropForeignKey('FK-user_commission_rules-updated_user', '{{%user_commission_rules}}');

		$this->dropTable('{{%user_commission_rules}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
