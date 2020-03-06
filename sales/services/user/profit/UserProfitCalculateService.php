<?php
namespace sales\services\user\profit;

use common\models\UserProductType;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\model\user\entity\profit\UserProfit;
use sales\repositories\user\UserProfitRepository;

/**
 * Class UserProfitCalculateService
 * @package sales\services\user\profit
 *
 * @property UserProfitRepository $userProfitRepository
 */
class UserProfitCalculateService
{
	/**
	 * @var UserProfitRepository
	 */
	private $userProfitRepository;

	public function __construct(UserProfitRepository $userProfitRepository)
	{
		$this->userProfitRepository = $userProfitRepository;
	}

	public function calculateByOrderUserProfit(ProductQuote $productQuote, Order $order): void
	{
		foreach ($order->orderUserProfit as $profit) {
			$userProfit = $this->userProfitRepository->findOrCreate($profit->oup_user_id, $profit->oup_order_id, $productQuote->pq_id, UserProfit::TYPE_USER_PROFIT);

			$userProductType = UserProductType::findOne(['upt_user_id' => $profit->oup_user_id, 'upt_product_type_id' => $productQuote->pqProduct->pr_type_id]);

			$userProductCommission = 0;
			if ($userProductType) {
				$userProductCommission = $userProductType->upt_commission_percent;
			}

			if ($userProfit->up_id) {
				$userProfit->updateProfit((new UserProfitCreateUpdateDTO(
					null,
					null,
					$order->or_id,
					$productQuote->pq_id,
					$userProductCommission,
					$productQuote->pq_profit_amount,
					$profit->oup_percent
				)));
			} else {
				$userProfit->create((new UserProfitCreateUpdateDTO(
					$profit->oup_user_id,
					$order->or_lead_id,
					$order->or_id,
					$productQuote->pq_id,
					$userProductCommission,
					$productQuote->pq_profit_amount,
					$profit->oup_percent,
					UserProfit::STATUS_PENDING,
					null,
					UserProfit::TYPE_USER_PROFIT
				)));
			}

			$this->userProfitRepository->save($userProfit);
		}
	}

	public function calculateByTipsUserProfit(Order $order): void
	{
		foreach($order->orderTipsUserProfit as $profit) {
			$userProfit = $this->userProfitRepository->findOrCreate($profit->otup_user_id, $profit->otup_order_id, null, UserProfit::TYPE_TIPS);

			if ($userProfit->up_id) {
				$userProfit->updateProfit((new UserProfitCreateUpdateDTO(
					null,
					null,
					$order->or_id,
					null,
					100,
					$profit->otup_amount,
					$profit->otup_percent
				)));
			} else {
				$userProfit->create((new UserProfitCreateUpdateDTO(
					$profit->otup_user_id,
					$order->or_lead_id,
					$order->or_id,
					null,
					100,
					$profit->otup_amount,
					$profit->otup_percent,
					UserProfit::STATUS_PENDING,
					null,
					UserProfit::TYPE_TIPS
				)));
			}

			$this->userProfitRepository->save($userProfit);
		}
	}
}