<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200609_123031_create_new_tbl_call_note
 */
class m200609_123031_create_new_tbl_call_note extends Migration
{
	private $routes = [
		'/call/ajax-add-note',
		'/call-note-crud/index',
		'/call-note-crud/update',
		'/call-note-crud/create',
		'/call-note-crud/delete',
		'/call-note-crud/view',
	];

	private $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_AGENT,
		Employee::ROLE_SUPERVISION,
		Employee::ROLE_QA,
		Employee::ROLE_USER_MANAGER,
		Employee::ROLE_SUP_AGENT,
		Employee::ROLE_SUP_SUPER,
		Employee::ROLE_EX_AGENT,
		Employee::ROLE_EX_SUPER,
		Employee::ROLE_SALES_SENIOR,
		Employee::ROLE_EXCHANGE_SENIOR,
		Employee::ROLE_SUPPORT_SENIOR,
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

		$this->createTable('{{%call_note}}',	[
			'cn_id'              		=> $this->primaryKey(),
			'cn_call_id'               	=> $this->integer(),
			'cn_note'     				=> $this->string(255),
			'cn_created_dt'             => $this->dateTime(),
			'cn_updated_dt'             => $this->dateTime(),
			'cn_created_user_id'        => $this->integer(),
			'cn_updated_user_id'        => $this->integer(),
		], $tableOptions);

		$this->addForeignKey('FK-call_note-cn_call_id', '{{%call_note}}', ['cn_call_id'], '{{%call}}', ['c_id'], 'CASCADE', 'CASCADE');

		$this->addForeignKey('FK-call_note-cn_created_user_id', '{{%call_note}}', ['cn_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-call_note-cn_updated_user_id', '{{%call_note}}', ['cn_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');

		(new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropTable('{{%call_note}}');

		(new RbacMigrationService())->down($this->routes, $this->roles);
	}
}
