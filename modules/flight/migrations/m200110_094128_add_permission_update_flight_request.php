<?php
namespace modules\flight\migrations;

use sales\rbac\rules\ProductOwnerRule;
use Yii;
use yii\db\Migration;

/**
 * Class m200110_094128_add_permission_update_flight_request
 */
class m200110_094128_add_permission_update_flight_request extends Migration
{
	public $permissions = [
		'updateProduct',
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
	 * @throws \yii\base\Exception
	 */
    public function safeUp()
    {
		$auth = Yii::$app->authManager;

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

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$auth = Yii::$app->authManager;

		$updateProduct = $auth->getPermission('updateProduct');
		$updateOwnProduct = $auth->getPermission('updateOwnProduct');

		$productOwnerRule = $auth->getRule('isProductOwner');
		$auth->remove($productOwnerRule);
		$auth->remove($updateProduct);
		$auth->remove($updateOwnProduct);

		if (Yii::$app->cache) {
			Yii::$app->cache->flush();
		}
	}
}
