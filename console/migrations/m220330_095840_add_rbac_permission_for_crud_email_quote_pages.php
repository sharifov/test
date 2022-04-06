<?php

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m220330_095840_add_rbac_permission_for_crud_email_quote_pages
 */
class m220330_095840_add_rbac_permission_for_crud_email_quote_pages extends Migration
{
    private array $routes = [
        '/email-quote-crud/index',
        '/email-quote-crud/create',
        '/email-quote-crud/update',
        '/email-quote-crud/delete',
        '/email-quote-crud/view',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN
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
        (new RbacMigrationService())->down($this->routes, $this->roles);
    }
}
