<?php

namespace modules\product\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m210727_075346_add_rbac_permission_for_product_quote_option_refund_crud_pages
 */
class m210727_075346_add_rbac_permission_for_product_quote_option_refund_crud_pages extends Migration
{
    private array $routes = [
        '/product/product-quote-option-refund-crud/index',
        '/product/product-quote-option-refund-crud/create',
        '/product/product-quote-option-refund-crud/update',
        '/product/product-quote-option-refund-crud/delete',
        '/product/product-quote-option-refund-crud/view',
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
