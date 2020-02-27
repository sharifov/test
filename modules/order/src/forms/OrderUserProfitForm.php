<?php
namespace modules\order\src\forms;

use common\models\Employee;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use yii\base\Model;

class OrderUserProfitForm extends Model
{
	public $oup_order_id;
	public $oup_user_id;
	public $oup_percent;
	public $oup_amount;

	public function rules()
	{
		return [
			[['oup_order_id', 'oup_user_id', 'oup_percent'], 'required'],
			[['oup_order_id', 'oup_user_id', 'oup_percent'], 'integer'],
			[['oup_amount', 'oup_percent'], 'number'],
			['oup_percent', 'integer', 'max' => OrderUserProfit::MAX_PERCENT , 'min' => OrderUserProfit::MIN_PERCENT],
			[['oup_order_id', 'oup_user_id'], 'unique', 'targetClass' => OrderUserProfit::class, 'targetAttribute' => ['oup_order_id', 'oup_user_id']],
			[['oup_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['oup_user_id' => 'id']],
			[['oup_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['oup_order_id' => 'or_id']],
			[['oup_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['oup_user_id' => 'id']],
			[['oup_percent'], 'checkPercentForAllUsersByOrder']
		];
	}

	public function checkPercentForAllUsersByOrder($attribute): void
	{
		$allOrders = OrderUserProfit::find()->select(['sum(oup_percent) as `total_percent_sum`'])->where(['oup_order_id' => $this->oup_order_id])->andWhere(['<>', 'oup_user_id', $this->oup_user_id])->asArray()->one();

		if (isset($allOrders['total_percent_sum'])) {
			$totalSum = $allOrders['total_percent_sum'] + $this->$attribute;

			if ($totalSum > OrderUserProfit::MAX_PERCENT) {
				$this->addError('oup_percent', 'Total sum of percent on this order cant be more then 100%');
			}
		}
	}

}