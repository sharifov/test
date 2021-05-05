<?php

namespace modules\order\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210407_090609_add_permission_for_order_contact_crud_pages
 */
class m210407_090609_add_permission_for_order_contact_crud_pages extends Migration
{
    private array $routes = [
        '/order/order-contact-crud/index',
        '/order/order-contact-crud/create',
        '/order/order-contact-crud/update',
        '/order/order-contact-crud/delete',
        '/order/order-contact-crud/view',
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
