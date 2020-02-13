<?php
namespace sales\repositories\user;

use sales\model\user\entity\payment\UserPayment;
use sales\repositories\Repository;

class UserPaymentRepository extends Repository
{
	/**
	 * @param int $paymentId
	 * @return UserPayment
	 */
	public function findById(int $paymentId): UserPayment
	{
		$payment = UserPayment::findOne($paymentId);
		if ($payment === null) {
			throw new \RuntimeException('User payment not fount');
		}
		return $payment;
	}

	/**
	 * @param UserPayment $userPayment
	 * @return int
	 */
	public function save(UserPayment $userPayment): int
	{
		if (!$userPayment->save()) {
			throw new \RuntimeException('User Payment saving error');
		}
		return $userPayment->upt_id;
	}
}