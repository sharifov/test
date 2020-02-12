<?php

namespace sales\model\user\profit;

use common\models\Employee;
use common\models\Lead;
use common\models\query\EmployeeQuery;
use common\models\query\LeadsQuery;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\orderProduct\Scopes;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\model\user\payroll\UserPayroll;
use sales\model\user\payroll\UserPayrollQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_profit".
 *
 * @property int $up_id
 * @property int $up_user_id
 * @property int|null $up_lead_id
 * @property int|null $up_order_id
 * @property int|null $up_product_quote_id
 * @property int|null $up_percent
 * @property float|null $up_profit
 * @property float|null $up_split_percent
 * @property float|null $up_amount
 * @property int|null $up_status_id
 * @property string|null $up_created_dt
 * @property string|null $up_updated_dt
 * @property int|null $up_payroll_id
 * @property int|null $up_type_id
 *
 * @property Lead $upLead
 * @property Order $upOrder
 * @property UserPayroll $upPayroll
 * @property ProductQuote $upProductQuote
 * @property Employee $upUser
 */
class UserProfit extends \yii\db\ActiveRecord
{
	public const STATUS_PENDING = 1;
	public const STATUS_DONE = 2;
	public const STATUS_CANCELED = 3;
	public const STATUS_DELETED = 4;

	public const STATUS_LIST = [
		self::STATUS_PENDING => 'Pending',
		self::STATUS_DONE => 'Done',
		self::STATUS_CANCELED => 'Canceled',
		self::STATUS_DELETED => 'Deleted'
	];

	public const TYPE_SALE_COMM = 1;
	public const TYPE_EXCHANGE_COMM = 2;
	public const TYPE_TIPS = 3;

	public const TYPE_LIST = [
		self::TYPE_SALE_COMM => 'Sale commission',
		self::TYPE_EXCHANGE_COMM => 'Exchange commission',
		self::TYPE_TIPS => 'Tips'
	];

	/**
	 * @return array
	 */
	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['up_created_dt', 'up_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['up_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s')
			],
		];
	}

	public function beforeSave($insert)
	{
		$this->calcAmount();
		return parent::beforeSave($insert);
	}

	/**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_profit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['up_user_id'], 'required'],
            [['up_id', 'up_user_id', 'up_lead_id', 'up_order_id', 'up_product_quote_id', 'up_percent', 'up_status_id', 'up_payroll_id', 'up_type_id'], 'integer'],
            [['up_profit', 'up_split_percent', 'up_amount'], 'number'],
            [['up_profit', 'up_split_percent', 'up_amount'], 'filter', 'filter' => 'intval'],
            [['up_created_dt', 'up_updated_dt'], 'safe'],
            [['up_id'], 'unique'],
			[['up_percent', 'up_split_percent'], 'number', 'max' => 100, 'min' => 0],
            [['up_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['up_lead_id' => 'id']],
            [['up_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['up_order_id' => 'or_id']],
            [['up_payroll_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserPayroll::class, 'targetAttribute' => ['up_payroll_id' => 'ups_id']],
            [['up_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['up_product_quote_id' => 'pq_id']],
            [['up_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['up_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'up_id' => 'ID',
            'up_user_id' => 'User',
            'up_lead_id' => 'Lead',
            'up_order_id' => 'Order',
            'up_product_quote_id' => 'Product Quote',
            'up_percent' => 'Percent',
            'up_profit' => 'Profit',
            'up_split_percent' => 'Split Percent',
            'up_amount' => 'Amount',
            'up_status_id' => 'Status',
            'up_created_dt' => 'Created Dt',
            'up_updated_dt' => 'Updated Dt',
            'up_payroll_id' => 'Payroll',
            'up_type_id' => 'Type',
        ];
    }

    /**
     * Gets query for [[UpLead]].
     *
     * @return ActiveQuery|LeadsQuery
     */
    public function getUpLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'up_lead_id']);
    }

    /**
     * Gets query for [[UpOrder]].
     *
     * @return ActiveQuery
     */
    public function getUpOrder(): ActiveQuery
	{
        return $this->hasOne(Order::class, ['or_id' => 'up_order_id']);
    }

    /**
     * Gets query for [[UpPayroll]].
     *
     * @return ActiveQuery|UserPayrollQuery
     */
    public function getUpPayroll()
    {
        return $this->hasOne(UserPayroll::class, ['ups_id' => 'up_payroll_id']);
    }

    /**
     * Gets query for [[UpProductQuote]].
     *
     * @return ActiveQuery|\modules\product\src\entities\productQuote\Scopes
     */
    public function getUpProductQuote()
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'up_product_quote_id']);
    }

    /**
     * Gets query for [[UpUser]].
     *
     * @return ActiveQuery|EmployeeQuery
     */
    public function getUpUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'up_user_id']);
    }

    public static function getStatusList(): array
	{
		return self::STATUS_LIST;
	}

	public static function getStatusName($statusId): ?string
	{
		return self::getStatusList()[$statusId] ?? null;
	}

	public static function getTypeList(): array
	{
		return self::TYPE_LIST;
	}

	public static function getTypeName($typeId): ?string
	{
		return self::getTypeList()[$typeId] ?? null;
	}

	public function calcAmount(): void
	{
		$this->up_amount = round((((float)$this->up_profit * (int)$this->up_percent / 100) * (int)$this->up_split_percent / 100), 2);
	}

    /**
     * {@inheritdoc}
     * @return UserProfitQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserProfitQuery(static::class);
    }
}
