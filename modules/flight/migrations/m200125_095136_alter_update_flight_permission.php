<?php
namespace modules\flight\migrations;

use console\migrations\RbacMigrationService;
use sales\rbac\rules\ProductOwnerRule;
use yii\db\Migration;

/**
 * Class m200125_095136_alter_update_flight_permission
 */
class m200125_095136_alter_update_flight_permission extends Migration
{
	public $routes = [
		'/flight/flight/ajax-update-itinerary-view',
		'/flight/flight/ajax-update-itinerary',
		'/flight/flight/ajax-search-quote',
		'/flight/flight/ajax-add-quote'
	];

	public $roles = [
		\common\models\Employee::ROLE_ADMIN,
		\common\models\Employee::ROLE_SUPER_ADMIN,
//		\common\models\Employee::ROLE_AGENT,
//		\common\models\Employee::ROLE_EX_AGENT,
//		\common\models\Employee::ROLE_EX_SUPER,
//		\common\models\Employee::ROLE_SUP_AGENT,
//		\common\models\Employee::ROLE_SUP_SUPER,
//		\common\models\Employee::ROLE_SUPERVISION,
	];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->removeOldPermission();

		(new RbacMigrationService())->up($this->routes, $this->roles);

		$this->flushCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->createOldPermission();

		(new RbacMigrationService())->down($this->routes, $this->roles);

		$this->flushCache();
    }

    private function removeOldPermission(): void
	{
		$auth = \Yii::$app->authManager;

		$updateProduct = $auth->getPermission('updateProduct');
		$updateOwnProduct = $auth->getPermission('updateOwnProduct');

		$productOwnerRule = $auth->getRule('isProductOwner');
		$auth->remove($productOwnerRule);
		$auth->remove($updateProduct);
		$auth->remove($updateOwnProduct);
	}

	private function createOldPermission(): void
	{
		$auth = \Yii::$app->authManager;

		$admin = $auth->getRole('admin');

		$updateProduct = $auth->createPermission('updateProduct');
		$updateProduct->description = 'Update Product';
		$auth->add($updateProduct);

		$auth->addChild($admin, $updateProduct);
		//----------------------------------------------------------

		//----------------------------------------------------------
		$updateProductRule = new ProductOwnerRule();
		$auth->add($updateProductRule);

		$updateOwnProduct = $auth->createPermission('updateOwnProduct');
		$updateOwnProduct->description = 'Update Own Product';
		$updateOwnProduct->ruleName = $updateProductRule->name;
		$auth->add($updateOwnProduct);

		$auth->addChild($updateOwnProduct, $updateProduct);
	}

	private function flushCache(): void
	{
		if (\Yii::$app->cache) {
			\Yii::$app->cache->flush();
		}
	}
}
