<?php

namespace sales\model\user\payment;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use sales\model\user\paymentCategory\UserPaymentCategory;
use sales\model\user\paymentCategory\UserPaymentCategoryQuery;
use sales\model\user\payroll\UserPayroll;
use sales\model\user\payroll\UserPayrollQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_payment".
 *
 * @property int $upt_id
 * @property int $upt_assigned_user_id
 * @property int $upt_category_id
 * @property int|null $upt_status_id
 * @property float|null $upt_amount
 * @property string|null $upt_description
 * @property string|null $upt_date
 * @property int|null $upt_created_user_id
 * @property int|null $upt_updated_user_id
 * @property string|null $upt_created_dt
 * @property string|null $upt_updated_dt
 * @property int|null $upt_payroll_id
 *
 * @property Employee $uptAssignedUser
 * @property UserPaymentCategory $uptCategory
 * @property Employee $uptCreatedUser
 * @property UserPayroll $uptPayroll
 * @property Employee $uptUpdatedUser
 */
class UserPayment extends \yii\db\ActiveRecord
{
	public const STATUS_PENDING = 1;
	public const STATUS_APPROVED = 2;
	public const STATUS_CANCELED = 3;
	public const STATUS_DELETED = 4;

	public const STATUS_LIST = [
		self::STATUS_PENDING => 'Pending',
		self::STATUS_APPROVED => 'Approved',
		self::STATUS_CANCELED => 'Canceled',
		self::STATUS_DELETED => 'Deleted'
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
					ActiveRecord::EVENT_BEFORE_INSERT => ['upt_created_dt', 'upt_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['upt_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s')
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'upt_created_user_id',
				'updatedByAttribute' => 'upt_updated_user_id',
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['upt_assigned_user_id', 'upt_category_id', 'upt_status_id'], 'required'],
            [['upt_id', 'upt_assigned_user_id', 'upt_category_id', 'upt_status_id', 'upt_created_user_id', 'upt_updated_user_id', 'upt_payroll_id'], 'integer'],
            [['upt_amount'], 'number'],
            [['upt_date', 'upt_created_dt', 'upt_updated_dt'], 'safe'],
            [['upt_description'], 'string', 'max' => 255],
            [['upt_id'], 'unique'],
            [['upt_date'], 'date', 'format' => 'php:Y-m-d'],
            [['upt_assigned_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upt_assigned_user_id' => 'id']],
            [['upt_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserPaymentCategory::class, 'targetAttribute' => ['upt_category_id' => 'upc_id']],
            [['upt_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upt_created_user_id' => 'id']],
            [['upt_payroll_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserPayroll::class, 'targetAttribute' => ['upt_payroll_id' => 'ups_id']],
            [['upt_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upt_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'upt_id' => 'ID',
            'upt_assigned_user_id' => 'Assigned User',
            'upt_category_id' => 'Category',
            'upt_status_id' => 'Status',
            'upt_amount' => 'Amount',
            'upt_description' => 'Description',
            'upt_date' => 'Date',
            'upt_created_user_id' => 'Created User',
            'upt_updated_user_id' => 'Updated User',
            'upt_created_dt' => 'Created',
            'upt_updated_dt' => 'Updated',
            'upt_payroll_id' => 'Payroll',
        ];
    }

    /**
     * Gets query for [[UptAssignedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUptAssignedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upt_assigned_user_id']);
    }

    /**
     * Gets query for [[UptCategory]].
     *
     * @return \yii\db\ActiveQuery|UserPaymentCategoryQuery
     */
    public function getUptCategory()
    {
        return $this->hasOne(UserPaymentCategory::class, ['upc_id' => 'upt_category_id']);
    }

    /**
     * Gets query for [[UptCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUptCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upt_created_user_id']);
    }

    /**
     * Gets query for [[UptPayroll]].
     *
     * @return \yii\db\ActiveQuery|UserPayrollQuery
     */
    public function getUptPayroll()
    {
        return $this->hasOne(UserPayroll::class, ['ups_id' => 'upt_payroll_id']);
    }

    /**
     * Gets query for [[UptUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUptUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upt_updated_user_id']);
    }

    public static function getStatusList(): array
	{
		return self::STATUS_LIST;
	}

	public static function getStatusName($statusId): ?string
	{
		return self::getStatusList()[$statusId];
	}

    /**
     * {@inheritdoc}
     * @return UserPaymentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserPaymentQuery(static::class);
    }
}
