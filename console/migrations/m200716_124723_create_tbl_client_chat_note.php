<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200716_124723_create_tbl_client_chat_note
 */
class m200716_124723_create_tbl_client_chat_note extends Migration
{
    private array $routes = [
		'/client-chat-note-crud/index',
		'/client-chat-note-crud/update',
		'/client-chat-note-crud/create',
		'/client-chat-note-crud/delete',
		'/client-chat-note-crud/view',
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
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{client_chat_note}}', [
			'ccn_id' => $this->primaryKey(),

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

			'ccn_created_dt' => $this->dateTime(),
			'ccn_updated_dt' => $this->dateTime()
		], $tableOptions);

		$this->addForeignKey('FK-client_chat_data-ccd_cch_id', '{{client_chat_data}}', ['ccd_cch_id'], '{{client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');

		(new RbacMigrationService())->up($this->routes, $this->roles);
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    	/* TODO::  */
    	/*$this->dropForeignKey('FK-client_chat_data-ccd_cch_id', '{{client_chat_data}}');
    	$this->dropTable('{{client_chat_data}}');*/

		(new RbacMigrationService())->down($this->routes, $this->roles);
	}
}
