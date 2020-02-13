<?php
namespace modules\product\src\grid\columns;

use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productType\ProductTypeQuery;
use modules\product\src\entities\productTypePaymentMethod\search\ProductTypePaymentMethodSearch;
use yii\grid\DataColumn;
use yii\helpers\Html;

class ProductTypeCountPaymentMethodsColumn extends DataColumn
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
		$count = $model->getProductTypePaymentMethod()->count();

		$searchClass = (new \ReflectionClass(ProductTypePaymentMethodSearch::class))->getShortName();

		if ($count) {
			$link = Html::a($count,
				[
					'/product/product-type-payment-method/index',
					$searchClass . '[ptpm_produt_type_id]' => $model->pt_id
				],
				[
					'data-pjax' => 0,
					'target' => '_blank'
				]);
		} else {
			$link = null;
		}

		return $link;
	}
}

