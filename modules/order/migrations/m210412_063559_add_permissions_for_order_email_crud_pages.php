<?php

namespace modules\order\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210412_063559_add_permissions_for_order_email_crud_pages
 */
class m210412_063559_add_permissions_for_order_email_crud_pages extends Migration
{
    private array $routes = [
        '/order/order-email-crud/index',
        '/order/order-email-crud/create',
        '/order/order-email-crud/update',
        '/order/order-email-crud/delete',
        '/order/order-email-crud/view',
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
