<?php

namespace sales\model\user\entity\payroll;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use sales\model\user\entity\payment\UserPayment;
use sales\model\user\entity\payment\UserPaymentQuery;
use sales\model\user\entity\profit\UserProfit;
use sales\model\user\entity\profit\UserProfitQuery;
use sales\services\user\payroll\UserPayrollCreateDTO;
use yii\behaviors\TimestampBehavior;
use yii\bootstrap4\Html;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_payroll".
 *
 * @property int $ups_id
 * @property int $ups_user_id
 * @property int $ups_month
 * @property int $ups_year
 * @property float|null $ups_base_amount
 * @property float|null $ups_profit_amount
 * @property float|null $ups_tax_amount
 * @property float|null $ups_payment_amount
 * @property float|null $ups_total_amount
 * @property int|null $ups_agent_status_id
 * @property int|null $ups_status_id
 * @property string|null $ups_created_dt
 * @property string|null $ups_updated_dt
 *
 * @property UserPayment[] $userPayments
 * @property Employee $upsUser
 * @property UserProfit[] $userProfits
 */
class UserPayroll extends \yii\db\ActiveRecord
{
	public const AGENT_STATUS_PENDING = 1;
	public const AGENT_STATUS_APPROVED = 2;
	public const AGENT_STATUS_NOT_APPROVED = 3;

	public const AGENT_STATUS_LIST = [
		self::AGENT_STATUS_PENDING => 'Pending',
		self::AGENT_STATUS_APPROVED => 'Approved',
		self::AGENT_STATUS_NOT_APPROVED => 'Not Approved'
	];

	public const STATUS_PENDING = 1;
	public const STATUS_APPROVED = 2;
	public const STATUS_NOT_APPROVED = 3;
	public const STATUS_PAID = 4;
	public const STATUS_RECALCULATED = 5;

	public const STATUS_LIST = [
		self::STATUS_PENDING => 'Pending',
		self::STATUS_APPROVED => 'Approved',
		self::STATUS_NOT_APPROVED => 'Not Approved',
		self::STATUS_PAID => 'Paid',
		self::STATUS_RECALCULATED => 'Recalculated'
	];

	public const STATUS_CLASS_LIST = [
		self::STATUS_PENDING => 'info',
		self::STATUS_APPROVED => 'success',
		self::STATUS_NOT_APPROVED => 'danger',
		self::STATUS_PAID => 'success',
		self::STATUS_RECALCULATED => 'warning',
	];

	public const AGENT_STATUS_CLASS_LIST = [
		self::AGENT_STATUS_PENDING => 'info',
		self::AGENT_STATUS_APPROVED => 'success',
		self::AGENT_STATUS_NOT_APPROVED => 'danger'
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
					ActiveRecord::EVENT_BEFORE_INSERT => ['ups_created_dt', 'ups_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['ups_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s')
			],
		];
	}

	/**
	 * @param bool $insert
	 * @return bool
	 */
	public function beforeSave($insert): bool
	{
		$totalAmount = (float)$this->ups_base_amount + (float)$this->ups_profit_amount + (float)$this->ups_tax_amount + (float)$this->ups_payment_amount;
		if ($this->ups_total_amount !== null && (float)$this->ups_total_amount !== $totalAmount) {
			$this->ups_status_id = self::STATUS_RECALCULATED;
		}
		$this->ups_total_amount = $totalAmount;
		return parent::beforeSave($insert);
	}

	/**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_payroll';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ups_user_id', 'ups_month', 'ups_year'], 'required'],
            [['ups_id', 'ups_user_id', 'ups_year', 'ups_agent_status_id', 'ups_status_id'], 'integer'],
            [['ups_base_amount', 'ups_profit_amount', 'ups_tax_amount', 'ups_payment_amount', 'ups_total_amount'], 'number'],
            [['ups_created_dt', 'ups_updated_dt'], 'safe'],
            [['ups_user_id', 'ups_month', 'ups_year'], 'unique', 'targetAttribute' => ['ups_user_id', 'ups_month', 'ups_year']],
            [['ups_id'], 'unique'],
            [['ups_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ups_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ups_user_id' => 'User',
            'ups_month' => 'Month',
            'ups_year' => 'Year',
            'ups_base_amount' => 'Base Amount',
            'ups_profit_amount' => 'Profit Amount',
            'ups_tax_amount' => 'Tax Amount',
            'ups_payment_amount' => 'Payment Amount',
            'ups_total_amount' => 'Total Amount',
            'ups_agent_status_id' => 'Agent Status',
            'ups_status_id' => 'Status',
            'ups_created_dt' => 'Created DT',
            'ups_updated_dt' => 'Updated DT',
        ];
    }

    /**
     * Gets query for [[UserPayments]].
     *
     * @return ActiveQuery|UserPaymentQuery
     */
    public function getUserPayments()
    {
        return $this->hasMany(UserPayment::class, ['upt_payroll_id' => 'ups_id']);
    }

    /**
     * Gets query for [[UpsUser]].
     *
     * @return ActiveQuery|EmployeeQuery
     */
    public function getUpsUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ups_user_id']);
    }

    /**
     * Gets query for [[UserProfits]].
     *
     * @return ActiveQuery|UserProfitQuery
     */
    public function getUserProfits()
    {
        return $this->hasMany(UserProfit::class, ['up_payroll_id' => 'ups_id']);
    }

	/**
	 * @return array
	 */
    public static function getAgentStatusList(): array
	{
		return self::AGENT_STATUS_LIST;
	}

	/**
	 * @param int $statusId
	 * @return string|null
	 */
	public static function getAgentStatusName(int $statusId): ?string
	{
		return self::getAgentStatusList()[$statusId] ?? null;
	}

	/**
	 * @return array
	 */
	public static function getStatusList(): array
	{
		return self::STATUS_LIST;
	}

	public static function getStatusName(int $statusId): ?string
	{
		return self::getStatusList()[$statusId] ?? null;
	}

    /**
     * {@inheritdoc}
     * @return UserPayrollQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserPayrollQuery(static::class);
    }

	/**
	 * @param UserPayrollCreateDTO $dto
	 * @return UserPayroll
	 */
    public static function create(UserPayrollCreateDTO $dto): self
	{
		$payroll = new self();

		$payroll->ups_user_id = $dto->userId;
		$payroll->ups_month = $dto->month;
		$payroll->ups_year = $dto->year;
		$payroll->ups_base_amount = $dto->baseAmount;
		$payroll->ups_profit_amount = $dto->profitAmount;
		$payroll->ups_tax_amount = $dto->taxAmount;
		$payroll->ups_payment_amount = $dto->paymentAmount;
		$payroll->ups_agent_status_id = $dto->agentStatus;
		$payroll->ups_status_id = $dto->status;

		return $payroll;
	}

	/**
	 * @param UserPayrollCreateDTO $dto
	 */
	public function recalculate(UserPayrollCreateDTO $dto): void
	{
		$this->ups_base_amount = $dto->baseAmount;
		$this->ups_profit_amount = $dto->profitAmount;
		$this->ups_tax_amount = $dto->taxAmount;
		$this->ups_payment_amount = $dto->paymentAmount;
	}

	/**
	 * @return mixed|null
	 */
	public function getRowClass()
	{
		return self::STATUS_CLASS_LIST[$this->ups_status_id] ?? null;
	}

	public static function asFormat(?int $value): ?string
	{
		return $value ? Html::tag(
			'span',
			self::getStatusName($value),
			['class' => 'badge badge-' . self::getClassName($value)]
		) : null;
	}

	public static function asFormatAgent(?int $value): ?string
	{
		return $value ? Html::tag(
			'span',
			self::getAgentStatusName($value),
			['class' => 'badge badge-' . self::getAgentClassName($value)]
		) : null;
	}

	private static function getAgentClassName(?int $value)
	{
		return self::AGENT_STATUS_CLASS_LIST[$value] ?? 'secondary';
	}

	private static function getClassName(?int $value): string
	{
		return self::STATUS_CLASS_LIST[$value] ?? 'secondary';
	}
}
