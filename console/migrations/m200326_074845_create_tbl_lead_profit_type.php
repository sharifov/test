<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200326_074845_create_tbl_lead_profit_type
 */
class m200326_074845_create_tbl_lead_profit_type extends Migration
{
	public $route = [
		'/lead-profit-type-crud',
		'/lead-profit-type-crud/index',
		'/lead-profit-type-crud/create',
		'/lead-profit-type-crud/view',
		'/lead-profit-type-crud/update',
		'/lead-profit-type-crud/delete',
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

		$this->createTable('{{%lead_profit_type}}', [
			'lpt_profit_type_id' => $this->smallInteger(),
			'lpt_diff_rule' => $this->tinyInteger(3),
			'lpt_commission_min' => $this->tinyInteger(3),
			'lpt_commission_max' => $this->tinyInteger(3),
			'lpt_commission_fix' => $this->tinyInteger(3),
			'lpt_created_user_id' => $this->integer(),
			'lpt_updated_user_id' => $this->integer(),
			'lpt_created_dt' => $this->dateTime(),
			'lpt_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addPrimaryKey('PK-lead_profit_type-lpt_profit_type_id', '{{%lead_profit_type}}', 'lpt_profit_type_id');

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
		$this->dropTable('{{%lead_profit_type}}');

		(new RbacMigrationService())->down($this->route, $this->roles);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
