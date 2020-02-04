<?php

namespace modules\product\src\entities\productTypePaymentMethod;

use common\models\PaymentMethod;

/**
 * This is the ActiveQuery class for [[ProductTypePaymentMethod]].
 *
 * @see ProductTypePaymentMethod
 */
class ProductTypePaymentMethodQuery
{
	/**
	 * @param int $productTypeId
	 * @return array|ProductTypePaymentMethod|\yii\db\ActiveRecord|null
	 */
	public static function getDefaultPaymentMethodByProductType(int $productTypeId)
	{
		return ProductTypePaymentMethod::find()->where(['ptpm_produt_type_id' => $productTypeId])->andWhere(['ptpm_default' => true])->one();
	}

	/**
	 * @param int $productTypeId
	 * @return float|null
	 */
	public static function getDefaultPercentFeeByProductType(int $productTypeId): ?float
	{
		$paymentMethod = self::getDefaultPaymentMethodByProductType($productTypeId);

		if ($paymentMethod) {
			return $paymentMethod->ptpm_payment_fee_percent;
		}

		\Yii::warning('Not Found default percent fee; Product Id . ' . $productTypeId, 'ProductTypePaymentMethodQuery:getDefaultPercentFeeByProductType');

		return null;
	}
}
