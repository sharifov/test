<?php

namespace modules\product\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210826_064624_add_new_rbac_permission_for_product_quote_data_crud_pages
 */
class m210826_064624_add_new_rbac_permission_for_product_quote_data_crud_pages extends Migration
{
    private array $routes = [
        '/product/product-quote-data-crud/index',
        '/product/product-quote-data-crud/create',
        '/product/product-quote-data-crud/update',
        '/product/product-quote-data-crud/delete',
        '/product/product-quote-data-crud/view',
    ];

    private array $roles = [
        Employee::ROLE_SUPER_ADMIN,
        Employee::ROLE_ADMIN,
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
