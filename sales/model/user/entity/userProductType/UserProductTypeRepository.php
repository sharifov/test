<?php
namespace sales\model\user\entity\userProductType;

use common\models\UserProductType;
use sales\repositories\Repository;

/**
 * Class UserProductTypeRepository
 * @package sales\model\user\userProductType
 */
class UserProductTypeRepository extends Repository
{
	/**
	 * @param int $userId
	 * @param int $productType
	 * @return UserProductType
	 */
	public function getByProductType(int $userId, int $productType): UserProductType
	{
		$userProductType = UserProductType::findOne(['upt_user_id' => $userId, 'upt_product_type_id' => $productType]);
		if (!$userProductType) {
			throw new \RuntimeException('User Product Type not found');
		}
		return $userProductType;
	}
}