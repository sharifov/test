<?php

namespace modules\product\migrations;

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210426_085420_add_permission_for_product_quote_lead_crud
 */
class m210426_085420_add_permission_for_product_quote_lead_crud extends Migration
{
    private array $route = [
        '/product/product-quote-lead/create',
        '/product/product-quote-lead/update',
        '/product/product-quote-lead/delete',
        '/product/product-quote-lead/view',
        '/product/product-quote-lead/index',
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
        (new \console\migrations\RbacMigrationService())->up($this->route, $this->roles);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        (new \console\migrations\RbacMigrationService())->down($this->route, $this->roles);
    }
}
