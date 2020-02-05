<?php

namespace common\models;

use common\models\query\PaymentMethodQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "payment_method".
 *
 * @property int $pm_id
 * @property string $pm_name
 * @property string|null $pm_short_name
 * @property int|null $pm_enabled
 * @property int|null $pm_category_id
 * @property int|null $pm_updated_user_id
 * @property string|null $pm_updated_dt
 *
 * @property Payment[] $payments
 * @property string $categoryName
 * @property Employee $pmUpdatedUser
 */
class PaymentMethod extends \yii\db\ActiveRecord
{

    public const CAT_CREDIT_CARD        = 1;
    public const CAT_DIRECT_DEBIT       = 2;
    public const CAT_EWALLET            = 3;
    public const CAT_BANK_TRANSFER      = 4;
    public const CAT_REALTIME_BANKING   = 5;
    public const CAT_CASH_PREPAID       = 6;
    public const CAT_MOBILE             = 7;

    public const CAT_LIST = [
        self::CAT_CREDIT_CARD        => 'Credit / Debit Card',
        self::CAT_DIRECT_DEBIT       => 'Direct Debit',
        self::CAT_EWALLET            => 'eWallets',
        self::CAT_BANK_TRANSFER      => 'Bank Transfers',
        self::CAT_REALTIME_BANKING   => 'Real-time Banking',
        self::CAT_CASH_PREPAID       => 'Cash & PrePaid Vouchers',
        self::CAT_MOBILE             => 'Mobile Payments'
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_method';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pm_name'], 'required'],
            [['pm_enabled', 'pm_category_id', 'pm_updated_user_id'], 'integer'],
            [['pm_updated_dt'], 'safe'],
            [['pm_name'], 'string', 'max' => 50],
            [['pm_short_name'], 'string', 'max' => 20],
            [['pm_name'], 'unique'],
            [['pm_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pm_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pm_id' => 'ID',
            'pm_name' => 'Name',
            'pm_short_name' => 'Short Name',
            'pm_enabled' => 'Enabled',
            'pm_category_id' => 'Category ID',
            'pm_updated_user_id' => 'Updated User ID',
            'pm_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pm_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pm_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'pm_updated_user_id',
                'updatedByAttribute' => 'pm_updated_user_id',
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPayments(): ActiveQuery
    {
        return $this->hasMany(Payment::class, ['pay_method_id' => 'pm_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPmUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pm_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return PaymentMethodQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PaymentMethodQuery(static::class);
    }

    /**
     * @return array
     */
    public static function getCategoryList(): array
    {
        return self::CAT_LIST;
    }

    /**
     * @return string
     */
    public function getCategoryName(): string
    {
        return self::CAT_LIST[$this->pm_category_id] ?? '';
    }

	/**
	 * @return array
	 */
    public static function getList(): array
	{
		return self::find()->select(['pm_name', 'pm_id'])->orderBy(['pm_name' => SORT_ASC])->indexBy('pm_id')->asArray()->column();
	}
}
