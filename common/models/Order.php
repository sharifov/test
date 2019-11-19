<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class ftable "order".
 *
 * @property int $or_id
 * @property string $or_gid
 * @property string $or_uid
 * @property string $or_name
 * @property int $or_lead_id
 * @property string $or_description
 * @property int $or_status_id
 * @property int $or_pay_status_id
 * @property string $or_app_total
 * @property string $or_app_markup
 * @property string $or_agent_markup
 * @property string $or_client_total
 * @property string $or_client_currency
 * @property string $or_client_currency_rate
 * @property int $or_owner_user_id
 * @property int $or_created_user_id
 * @property int $or_updated_user_id
 * @property string $or_created_dt
 * @property string $or_updated_dt
 *
 * @property Invoice[] $invoices
 * @property Lead $orLead
 * @property Employee $orCreatedUser
 * @property Employee $orOwnerUser
 * @property Employee $orUpdatedUser
 * @property ProductQuote[] $productQuotes
 */
class Order extends \yii\db\ActiveRecord
{

    public const STATUS_PENDING         = 1;
    public const STATUS_IN_PROGRESS     = 2;
    public const STATUS_DONE            = 3;
    public const STATUS_MODIFIED        = 4;
    public const STATUS_DECLINED        = 5;
    public const STATUS_CANCELED        = 6;

    public const STATUS_LIST        = [
        self::STATUS_PENDING        => 'Pending',
        self::STATUS_IN_PROGRESS    => 'In progress',
        self::STATUS_DONE           => 'Done',
        self::STATUS_MODIFIED       => 'Modified',
        self::STATUS_DECLINED       => 'Declined',
        self::STATUS_CANCELED       => 'Canceled',
    ];

    public const PAY_STATUS_NOT_PAID        = 1;
    public const PAY_STATUS_PAID            = 2;
    public const PAY_STATUS_PARTIAL_PAID    = 3;

    public const PAY_STATUS_LIST        = [
        self::PAY_STATUS_NOT_PAID           => 'Not paid',
        self::PAY_STATUS_PAID               => 'Paid',
        self::PAY_STATUS_PARTIAL_PAID       => 'Partial paid',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['or_gid', 'or_lead_id'], 'required'],
            [['or_lead_id', 'or_status_id', 'or_pay_status_id', 'or_owner_user_id', 'or_created_user_id', 'or_updated_user_id'], 'integer'],
            [['or_description'], 'string'],
            [['or_app_total', 'or_app_markup', 'or_agent_markup', 'or_client_total', 'or_client_currency_rate'], 'number'],
            [['or_created_dt', 'or_updated_dt'], 'safe'],
            [['or_gid'], 'string', 'max' => 32],
            [['or_uid'], 'string', 'max' => 15],
            [['or_name'], 'string', 'max' => 40],
            [['or_client_currency'], 'string', 'max' => 3],
            [['or_gid'], 'unique'],
            [['or_uid'], 'unique'],
            [['or_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['or_lead_id' => 'id']],
            [['or_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['or_created_user_id' => 'id']],
            [['or_owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['or_owner_user_id' => 'id']],
            [['or_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['or_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'or_id' => 'ID',
            'or_gid' => 'GID',
            'or_uid' => 'UID',
            'or_name' => 'Name',
            'or_lead_id' => 'Lead ID',
            'or_description' => 'Description',
            'or_status_id' => 'Status ID',
            'or_pay_status_id' => 'Pay Status ID',
            'or_app_total' => 'App Total',
            'or_app_markup' => 'App Markup',
            'or_agent_markup' => 'Agent Markup',
            'or_client_total' => 'Client Total',
            'or_client_currency' => 'Client Currency',
            'or_client_currency_rate' => 'Client Currency Rate',
            'or_owner_user_id' => 'Owner User ID',
            'or_created_user_id' => 'Created User ID',
            'or_updated_user_id' => 'Updated User ID',
            'or_created_dt' => 'Created Dt',
            'or_updated_dt' => 'Updated Dt',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['or_created_dt', 'or_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['or_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'or_created_user_id',
                'updatedByAttribute' => 'or_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::class, ['inv_order_id' => 'or_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'or_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'or_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrOwnerUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'or_owner_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'or_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductQuotes()
    {
        return $this->hasMany(ProductQuote::class, ['pq_order_id' => 'or_id']);
    }

    /**
     * {@inheritdoc}
     * @return OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    /**
     * @return array
     */
    public static function getPayStatusList(): array
    {
        return self::PAY_STATUS_LIST;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->or_status_id] ?? '';
    }

    /**
     * @return string
     */
    public function getPayStatusName(): string
    {
        return self::STATUS_LIST[$this->or_pay_status_id] ?? '';
    }
}
