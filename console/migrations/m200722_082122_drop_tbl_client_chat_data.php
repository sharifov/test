<?php

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m200722_082122_drop_tbl_client_chat_data
 */
class m200722_082122_drop_tbl_client_chat_data extends Migration
{
	private array $routes = [
		'/client-chat-data-crud/index',
		'/client-chat-data-crud/update',
		'/client-chat-data-crud/create',
		'/client-chat-data-crud/delete',
		'/client-chat-data-crud/view',
	];

	private array $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropTable('{{%client_chat_data}}');

		(new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{client_chat_data}}', [
			'ccd_cch_id' => $this->primaryKey(),
			'ccd_country' => $this->string(50),
			'ccd_region' => $this->string(5),
			'ccd_city' => $this->string(50),
			'ccd_latitude' => $this->float(),
			'ccd_longitude' => $this->float(),
			'ccd_url' => $this->string(50),
			'ccd_title' => $this->string(50),
			'ccd_referrer' => $this->string(50),
			'ccd_timezone' => $this->string(50),
			'ccd_local_time' => $this->string(10),
			'ccd_created_dt' => $this->dateTime(),
			'ccd_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addForeignKey('FK-client_chat_data-ccd_cch_id', '{{client_chat_data}}', ['ccd_cch_id'], '{{client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');

		(new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);
	}
}
