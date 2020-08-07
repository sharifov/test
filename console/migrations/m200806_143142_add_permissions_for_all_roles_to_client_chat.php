<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200806_143142_add_permissions_for_all_roles_to_client_chat
 */
class m200806_143142_add_permissions_for_all_roles_to_client_chat extends Migration
{
	public $routes = [
		'/client-chat/index',
		'/client-chat/info',
		'/client-chat/note',
		'/client-chat/create-note',
		'/client-chat/delete-note',
		'/client-chat/access-manage',
		'/client-chat/ajax-data-info',
		'/client-chat/close',
		'/client-chat/ajax-history',
		'/client-chat/ajax-transfer-view',
		'/client-chat/refresh-notification',
		'/client-chat/discard-unread-messages',
		'/client-chat/send-offer-list',
		'/client-chat/send-offer-generate',
		'/client-chat/send-offer',
	];

	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
		Employee::ROLE_AGENT,
		Employee::ROLE_SUPERVISION,
		Employee::ROLE_SUP_AGENT,
		Employee::ROLE_EX_AGENT,
		Employee::ROLE_EX_SUPER,
		Employee::ROLE_SALES_SENIOR,
		Employee::ROLE_EXCHANGE_SENIOR,
		Employee::ROLE_SUP_SUPER,
		Employee::ROLE_QA,
		Employee::ROLE_QA_SUPER,
		Employee::ROLE_SUPPORT_SENIOR,
		Employee::ROLE_USER_MANAGER,
	];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		(new RbacMigrationService())->up($this->routes, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		(new RbacMigrationService())->up($this->routes, $this->roles);
	}
}
