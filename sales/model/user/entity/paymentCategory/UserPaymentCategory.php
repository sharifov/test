<?php

namespace sales\model\user\entity\paymentCategory;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use sales\model\user\entity\payment\UserPayment;
use sales\model\user\entity\payment\UserPaymentQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_payment_category".
 *
 * @property int $upc_id
 * @property string|null $upc_name
 * @property string|null $upc_description
 * @property int|null $upc_enabled
 * @property int|null $upc_created_user_id
 * @property int|null $upc_updated_user_id
 * @property string|null $upc_created_dt
 * @property string|null $upc_updated_dt
 *
 * @property UserPayment[] $userPayments
 * @property Employee $upcCreatedUser
 * @property Employee $upcUpdatedUser
 */
class UserPaymentCategory extends \yii\db\ActiveRecord
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

	/**
	 * @return array
	 */
	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['upc_created_dt', 'upc_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['upc_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s')
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'upc_created_user_id',
				'updatedByAttribute' => 'upc_updated_user_id',
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_payment_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['upc_id', 'upc_enabled', 'upc_created_user_id', 'upc_updated_user_id'], 'integer'],
            [['upc_created_dt', 'upc_updated_dt'], 'safe'],
            [['upc_name'], 'required'],
            [['upc_name'], 'string', 'max' => 30],
            [['upc_description'], 'string', 'max' => 255],
            [['upc_id'], 'unique'],
            [['upc_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upc_created_user_id' => 'id']],
            [['upc_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upc_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'upc_id' => 'Upc ID',
            'upc_name' => 'Upc Name',
            'upc_description' => 'Upc Description',
            'upc_enabled' => 'Upc Enabled',
            'upc_created_user_id' => 'Upc Created User ID',
            'upc_updated_user_id' => 'Upc Updated User ID',
            'upc_created_dt' => 'Upc Created Dt',
            'upc_updated_dt' => 'Upc Updated Dt',
        ];
    }

    /**
     * Gets query for [[UserPayments]].
     *
     * @return \yii\db\ActiveQuery|UserPaymentQuery
     */
    public function getUserPayments()
    {
        return $this->hasMany(UserPayment::class, ['upt_category_id' => 'upc_id']);
    }

    /**
     * Gets query for [[UpcCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUpcCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upc_created_user_id']);
    }

    /**
     * Gets query for [[UpcUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUpcUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upc_updated_user_id']);
    }

	/**
	 * @return array|UserPaymentCategory[]
	 */
    public static function getList(): array
	{
		return self::find()->select(['upc_name', 'upc_id'])->indexBy('upc_id')->asArray()->column();
	}

	/**
	 * @return array
	 */
	public static function getStatusList(): array
	{
		return self::STATUS_LIST;
	}

	/**
	 * @param int $statusId
	 * @return string|null
	 */
	public static function getStatusName(int $statusId): ?string
	{
		return self::getStatusList()[$statusId] ?? null;
	}

    /**
     * {@inheritdoc}
     * @return UserPaymentCategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserPaymentCategoryQuery(static::class);
    }
}
