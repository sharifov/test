<?php
namespace sales\yii\grid\userPayment;

use sales\model\user\entity\payment\UserPayment;
use yii\grid\DataColumn;

class UserPaymentStatusIdColumn extends DataColumn
{
	public $filter;
	public $format = 'raw';

	public function init(): void
	{
		parent::init();

		if ($this->filter === null) {
			$this->filter = UserPayment::getStatusList();
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
		return UserPayment::asFormat($model->{$this->attribute});
	}
}