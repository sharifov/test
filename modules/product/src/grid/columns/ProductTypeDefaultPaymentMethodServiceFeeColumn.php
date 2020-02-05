<?php


namespace modules\product\src\grid\columns;


use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethodQuery;
use yii\grid\DataColumn;
use yii\helpers\Html;

class ProductTypeDefaultPaymentMethodServiceFeeColumn extends DataColumn
{
	private const LABEL = 'Count Payment Methods';

	public $format = 'raw';

	public function init(): void
	{
		parent::init();

		if ($this->label === null) {
			$this->label = self::LABEL;
		}
	}

	/**
	 * @param $model ProductType
	 * @param $key
	 * @param $index
	 * @return string|null
	 * @throws \ReflectionException
	 */
	public function getDataCellValue($model, $key, $index)
	{
		$defaultPaymentMethod = ProductTypePaymentMethodQuery::getDefaultPaymentMethodByProductType($model->pt_id);

		if ($defaultPaymentMethod !== null) {
			$link = HTML::a(
				$defaultPaymentMethod->ptpm_payment_fee_percent . ' %',
				[
					'/product/product-type-payment-method/view',
					'ptpm_produt_type_id' => $defaultPaymentMethod->ptpm_produt_type_id,
					'ptpm_payment_method_id' => $defaultPaymentMethod->ptpm_payment_method_id
				],
				[
					'data-pjax' => 0,
					'target' => '_blank'
				]
			);
		} else {
			$link = null;
		}

		return $link;
	}
}