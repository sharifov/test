<?php

namespace common\models;

use common\models\query\CreditCardQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "credit_card".
 *
 * @property int $cc_id
 * @property string $cc_number
 * @property string|null $cc_display_number
 * @property string|null $cc_holder_name
 * @property int $cc_expiration_month
 * @property int $cc_expiration_year
 * @property string|null $cc_cvv
 * @property int|null $cc_type_id
 * @property int|null $cc_status_id
 * @property int|null $cc_is_expired
 * @property int|null $cc_created_user_id
 * @property int|null $cc_updated_user_id
 * @property string|null $cc_created_dt
 * @property string|null $cc_updated_dt
 *
 * @property BillingInfo[] $billingInfos
 * @property Employee $ccCreatedUser
 * @property string $typeName
 * @property Employee $ccUpdatedUser
 */
class CreditCard extends ActiveRecord
{

    public const TYPE_VISA              =   1;
    public const TYPE_MASTER_CARD       =   2;
    public const TYPE_AMERICAN_EXPRESS  =   3;
    public const TYPE_DISCOVER          =   4;
    public const TYPE_DINERS_CLUB       =   5;
    public const TYPE_JCB               =   6;


    public const TYPE_LIST = [
        self::TYPE_VISA                =>   'Visa',
        self::TYPE_MASTER_CARD         =>   'Master Card',
        self::TYPE_AMERICAN_EXPRESS    =>   'American Express',
        self::TYPE_DISCOVER            =>   'Discover',
        self::TYPE_DINERS_CLUB         =>   'Diners Club',
        self::TYPE_JCB                 =>   'JCB'
    ];

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'credit_card';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['cc_number', 'cc_expiration_month', 'cc_expiration_year'], 'required'],
            [['cc_expiration_month', 'cc_expiration_year', 'cc_type_id', 'cc_status_id', 'cc_is_expired', 'cc_created_user_id', 'cc_updated_user_id'], 'integer'],
            [['cc_created_dt', 'cc_updated_dt'], 'safe'],
            [['cc_number'], 'string', 'max' => 32],
            [['cc_display_number'], 'string', 'max' => 18],
            [['cc_holder_name'], 'string', 'max' => 50],
            [['cc_cvv'], 'string', 'max' => 16],
            [['cc_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cc_created_user_id' => 'id']],
            [['cc_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cc_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cc_id' => 'ID',
            'cc_number' => 'Number',
            'cc_display_number' => 'Display Number',
            'cc_holder_name' => 'Holder Name',
            'cc_expiration_month' => 'Expiration Month',
            'cc_expiration_year' => 'Expiration Year',
            'cc_cvv' => 'Cvv',
            'cc_type_id' => 'Type ID',
            'cc_status_id' => 'Status ID',
            'cc_is_expired' => 'Is Expired',
            'cc_created_user_id' => 'Created User ID',
            'cc_updated_user_id' => 'Updated User ID',
            'cc_created_dt' => 'Created Dt',
            'cc_updated_dt' => 'Updated Dt',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cc_created_dt', 'cc_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'cc_created_user_id',
                'updatedByAttribute' => 'cc_updated_user_id',
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBillingInfos(): ActiveQuery
    {
        return $this->hasMany(BillingInfo::class, ['bi_cc_id' => 'cc_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCcCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cc_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCcUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cc_updated_user_id']);
    }

    /**
     * @return CreditCardQuery the active query used by this AR class.
     */
    public static function find(): CreditCardQuery
    {
        return new CreditCardQuery(static::class);
    }

    /**
     * @return array
     */
    public static function getTypeList(): array
    {
        return self::TYPE_LIST;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return self::TYPE_LIST[$this->cc_type_id] ?? '';
    }
}
