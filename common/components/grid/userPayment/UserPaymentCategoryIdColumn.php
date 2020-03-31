<?php
namespace common\components\grid\userPayment;

use sales\model\user\entity\paymentCategory\UserPaymentCategory;
use yii\grid\DataColumn;

class UserPaymentCategoryIdColumn extends DataColumn
{
	public $filter;

	public $relation;

	public function init(): void
	{
		parent::init();

		if (empty($this->relation)) {
			throw new \InvalidArgumentException('relation must be set.');
		}

		if ($this->filter === null) {
			$this->filter = UserPaymentCategory::getList();
		}
	}

	/**
	 * @param mixed $model
	 * @param mixed $key
	 * @param int $index
	 * @return string|null
	 */
	public function getDataCellValue($model, $key, $index)
	{
		if ($model->{$this->attribute} && ($category = $model->{$this->relation})) {
			/** @var UserPaymentCategory $category */
			return $category->upc_name;
		}
		return null;
	}
}