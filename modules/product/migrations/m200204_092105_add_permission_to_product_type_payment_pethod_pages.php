<?php
namespace modules\product\migrations;

use common\models\Employee;
use console\migrations\RbacMigrationService;
use yii\db\Migration;

/**
 * Class m200204_092105_add_permission_to_product_type_payment_pethod_pages
 */
class m200204_092105_add_permission_to_product_type_payment_pethod_pages extends Migration
{
	public $roles = [
		Employee::ROLE_ADMIN,
		Employee::ROLE_SUPER_ADMIN,
	];

	public $routes = [
		'/product/product-type-payment-method/create',
		'/product/product-type-payment-method/delete',
		'/product/product-type-payment-method/index',
		'/product/product-type-payment-method/update',
		'/product/product-type-payment-method/view',
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
