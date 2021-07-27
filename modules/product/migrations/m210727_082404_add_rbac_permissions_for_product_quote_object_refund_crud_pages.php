<?php

namespace modules\product\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210727_082404_add_rbac_permissions_for_product_quote_object_refund_crud_pages
 */
class m210727_082404_add_rbac_permissions_for_product_quote_object_refund_crud_pages extends Migration
{
    private array $routes = [
        '/product/product-quote-object-refund-crud/index',
        '/product/product-quote-object-refund-crud/create',
        '/product/product-quote-object-refund-crud/update',
        '/product/product-quote-object-refund-crud/delete',
        '/product/product-quote-object-refund-crud/view',
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
