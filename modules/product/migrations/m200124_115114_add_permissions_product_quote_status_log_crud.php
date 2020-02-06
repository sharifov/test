<?php

namespace modules\product\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200124_115114_add_permissions_product_quote_status_log_crud
 */
class m200124_115114_add_permissions_product_quote_status_log_crud extends Migration
{
    public $roles = [
        Employee::ROLE_ADMIN,
        Employee::ROLE_SUPER_ADMIN,
    ];

    public $routes = [
        '/product/product-quote-status-log-crud/index',
        '/product/product-quote-status-log-crud/view',
        '/product/product-quote-status-log-crud/create',
        '/product/product-quote-status-log-crud/update',
        '/product/product-quote-status-log-crud/delete',
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
