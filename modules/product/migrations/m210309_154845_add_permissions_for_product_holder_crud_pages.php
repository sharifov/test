<?php

namespace modules\product\migrations;

use common\models\Employee;
use yii\db\Migration;

/**
 * Class m210309_154845_add_permissions_for_product_holder_crud_pages
 */
class m210309_154845_add_permissions_for_product_holder_crud_pages extends Migration
{
    private array $route = [
        '/product/product-holder-crud/create',
        '/product/product-holder-crud/update',
        '/product/product-holder-crud/delete',
        '/product/product-holder-crud/view',
        '/product/product-holder-crud/index',
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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210309_154845_add_permissions_for_product_holder_crud_pages cannot be reverted.\n";

        return false;
    }
    */
}
