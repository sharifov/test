<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200327_073959_create_tbl_user_bonus_rules
 */
class m200327_073959_create_tbl_user_bonus_rules extends Migration
{
	public $route = [
		'/user-bonus-rules-crud',
		'/user-bonus-rules-crud/index',
		'/user-bonus-rules-crud/create',
		'/user-bonus-rules-crud/view',
		'/user-bonus-rules-crud/update',
		'/user-bonus-rules-crud/delete',
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

		$this->createTable('{{%user_bonus_rules}}', [
			'ubr_exp_month' => $this->smallInteger(),
			'ubr_kpi_percent' => $this->smallInteger(),
			'ubr_order_profit' => $this->integer(),
			'ubr_value' => $this->decimal(8,2),
			'ubr_created_user_id' => $this->integer(),
			'ubr_updated_user_id' => $this->integer(),
			'ubr_created_dt' => $this->dateTime(),
			'ubr_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addPrimaryKey('PK-user_bonus_rules', '{{%user_bonus_rules}}', [
			'ubr_exp_month', 'ubr_kpi_percent', 'ubr_order_profit'
		]);

		$this->addForeignKey('FK-user_bonus_rules-created_user', '{{%user_bonus_rules}}', 'ubr_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		$this->addForeignKey('FK-user_bonus_rules-updated_user', '{{%user_bonus_rules}}', 'ubr_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		(new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		(new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);

		$this->dropForeignKey('FK-user_bonus_rules-created_user', '{{%user_bonus_rules}}');
		$this->dropForeignKey('FK-user_bonus_rules-updated_user', '{{%user_bonus_rules}}');

		$this->dropTable('{{%user_bonus_rules}}');

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }
}
