<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200424_085000_add_permissions_for_contacts
 */
class m200424_085000_add_permissions_for_contacts extends Migration
{
    public $routesContacts = [
        '/contacts/*',
    ];

    public $rolesContacts = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_AGENT,
        Employee::ROLE_SUPERVISION,
        Employee::ROLE_QA,
        Employee::ROLE_QA_SUPER,
        Employee::ROLE_USER_MANAGER,
        Employee::ROLE_SUP_AGENT,
        Employee::ROLE_EX_AGENT,
        Employee::ROLE_EX_SUPER,
        Employee::ROLE_SALES_SENIOR,
    ];

    public $routes = [
        '/user-contact-list/*',
        '/client-project/*',
    ];

    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        (new RbacMigrationService())->up($this->routesContacts, $this->rolesContacts);
        (new RbacMigrationService())->up($this->routes, $this->roles);

        if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new RbacMigrationService())->down($this->routesContacts, $this->rolesContacts);
        (new RbacMigrationService())->up($this->routes, $this->roles);

        if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
    }
}
