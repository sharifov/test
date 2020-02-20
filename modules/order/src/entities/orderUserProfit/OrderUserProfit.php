<?php

namespace modules\order\src\entities\orderUserProfit;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\events\ProductQuoteCalculateUserProfitEvent;
use sales\entities\EventTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_user_profit".
 *
 * @property int $oup_order_id
 * @property int $oup_user_id
 * @property int $oup_percent
 * @property float|null $oup_amount
 * @property string|null $oup_created_dt
 * @property string|null $oup_updated_dt
 * @property int|null $oup_created_user_id
 * @property int|null $oup_updated_user_id
 *
 * @property Employee $oupCreatedUser
 * @property Order $oupOrder
 * @property Employee $oupUpdatedUser
 * @property Employee $oupUser
 */
class OrderUserProfit extends \yii\db\ActiveRecord
{
	use EventTrait;

	public const MAX_PERCENT = 100;
	public const MIN_PERCENT = 0;

	public const SCENARIO_CRUD = 'crud';

	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CRUD] = ['oup_percent'];
		return $scenarios;
	}

	/**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_user_profit';
    }

	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['oup_created_dt', 'oup_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['oup_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'oup_created_user_id',
				'updatedByAttribute' => 'oup_updated_user_id',
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['oup_order_id', 'oup_user_id', 'oup_percent'], 'required'],
            [['oup_order_id', 'oup_user_id', 'oup_percent', 'oup_created_user_id', 'oup_updated_user_id'], 'integer'],
            [['oup_amount', 'oup_percent'], 'number'],
			['oup_percent', 'integer', 'max' => self::MAX_PERCENT , 'min' => self::MIN_PERCENT],
			[['oup_created_dt', 'oup_updated_dt'], 'safe'],
            [['oup_order_id', 'oup_user_id'], 'unique', 'targetAttribute' => ['oup_order_id', 'oup_user_id']],
            [['oup_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['oup_created_user_id' => 'id']],
            [['oup_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['oup_user_id' => 'id']],
            [['oup_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['oup_order_id' => 'or_id']],
            [['oup_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['oup_updated_user_id' => 'id']],
            [['oup_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['oup_user_id' => 'id']],
			[['oup_percent'], 'checkPercentForAllUsersByOrder', 'on' => 'crud']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'oup_order_id' => 'Order ID',
            'oup_user_id' => 'User ID',
            'oup_percent' => 'Percent',
            'oup_amount' => 'Amount',
            'oup_created_dt' => 'Created Dt',
            'oup_updated_dt' => 'Updated Dt',
            'oup_created_user_id' => 'Created User ID',
            'oup_updated_user_id' => 'Updated User ID',
        ];
    }

    /**
     * Gets query for [[OupCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getOupCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'oup_created_user_id']);
    }

    /**
     * Gets query for [[OupOrder]].
     *
     * @return \yii\db\ActiveQuery|\modules\order\src\entities\order\Scopes
     */
    public function getOupOrder()
    {
        return $this->hasOne(Order::class, ['or_id' => 'oup_order_id']);
    }

    /**
     * Gets query for [[OupUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getOupUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'oup_updated_user_id']);
    }

    /**
     * Gets query for [[OupUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getOupUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'oup_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }

    public function checkPercentForAllUsersByOrder($attribute): void
	{
		$allOrders = self::find()->select(['sum(oup_percent) as `total_percent_sum`'])->where(['oup_order_id' => $this->oup_order_id])->andWhere(['<>', 'oup_user_id', $this->oup_user_id])->asArray()->one();

		if (isset($allOrders['total_percent_sum'])) {
			$totalSum = $allOrders['total_percent_sum'] + $this->$attribute;

			if ($totalSum > self::MAX_PERCENT) {
				$this->addError('oup_percent', 'Total sum of percent on this order cant be more then 100%');
			}
		}
	}

	public function create(int $orderId, int $userId, int $percent = null, float $amount = null): self
	{
		$this->oup_order_id = $orderId;
		$this->oup_user_id = $userId;
		$this->oup_percent = $percent;
		$this->oup_amount = $amount;

		return $this;
	}
}
