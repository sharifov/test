<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m201001_135248_create_tbl_client_chat_action_reason
 */
class m201001_135248_create_tbl_client_chat_action_reason extends Migration
{
	public $route = [
		'/client-chat-action-reason-crud/index',
		'/client-chat-action-reason-crud/view',
		'/client-chat-action-reason-crud/create',
		'/client-chat-action-reason-crud/update',
		'/client-chat-action-reason-crud/delete',
	];

	public $role = [
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
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

		$tblName = '{{%client_chat_action_reason}}';

		$this->createTable($tblName, [
			'ccar_id' => $this->primaryKey(),
			'ccar_action_id' => $this->integer()->notNull(),
			'ccar_key' => $this->string(50),
			'ccar_name' => $this->string(50),
			'ccar_enabled' => $this->boolean(),
			'ccar_comment_required' => $this->boolean(),
			'ccar_created_user_id' => $this->integer(),
			'ccar_updated_user_id' => $this->integer(),
			'ccar_created_dt' => $this->dateTime(),
			'ccar_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addForeignKey('FK-client_chat_action_reason-ccar_created_user_id', $tblName, 'ccar_created_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-client_chat_action_reason-ccar_updated_user_id', $tblName, 'ccar_updated_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');

		(new \console\migrations\RbacMigrationService())->up($this->route, $this->role);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropTable('{{%client_chat_action_reason}}');

		(new \console\migrations\RbacMigrationService())->down($this->route, $this->role);
	}
}
