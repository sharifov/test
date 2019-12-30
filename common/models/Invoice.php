<?php

namespace common\models;

use common\models\query\InvoiceQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "invoice".
 *
 * @property int $inv_id
 * @property string $inv_gid
 * @property string $inv_uid
 * @property int $inv_order_id
 * @property int $inv_status_id
 * @property string $inv_sum
 * @property string $inv_client_sum
 * @property string $inv_client_currency
 * @property string $inv_currency_rate
 * @property string $inv_description
 * @property int $inv_created_user_id
 * @property int $inv_updated_user_id
 * @property string $inv_created_dt
 * @property string $inv_updated_dt
 *
 * @property Currency $invClientCurrency
 * @property Employee $invCreatedUser
 * @property Order $invOrder
 * @property string $statusName
 * @property string $statusLabel
 * @property string $statusClassName
 * @property Employee $invUpdatedUser
 */
class Invoice extends ActiveRecord
{

    public const STATUS_NOT_PAID        = 1;
    public const STATUS_PAID            = 2;
    public const STATUS_PARTIAL_PAID    = 3;

    public const STATUS_LIST        = [
        self::STATUS_NOT_PAID           => 'Not paid',
        self::STATUS_PAID               => 'Paid',
        self::STATUS_PARTIAL_PAID       => 'Partial paid',
    ];

    public const STATUS_CLASS_LIST        = [
        self::STATUS_NOT_PAID           => 'warning',
        self::STATUS_PAID               => 'success',
        self::STATUS_PARTIAL_PAID       => 'info',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inv_gid', 'inv_order_id', 'inv_sum', 'inv_client_sum'], 'required'],
            [['inv_order_id', 'inv_status_id', 'inv_created_user_id', 'inv_updated_user_id'], 'integer'],
            [['inv_sum', 'inv_client_sum', 'inv_currency_rate'], 'number'],
            [['inv_description'], 'string'],
            [['inv_created_dt', 'inv_updated_dt'], 'safe'],
            [['inv_gid'], 'string', 'max' => 32],
            [['inv_uid'], 'string', 'max' => 15],
            [['inv_client_currency'], 'string', 'max' => 3],
            [['inv_gid'], 'unique'],
            [['inv_uid'], 'unique'],
            [['inv_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['inv_client_currency' => 'cur_code']],
            [['inv_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['inv_created_user_id' => 'id']],
            [['inv_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['inv_order_id' => 'or_id']],
            [['inv_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['inv_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inv_id' => 'ID',
            'inv_gid' => 'GID',
            'inv_uid' => 'UID',
            'inv_order_id' => 'Order ID',
            'inv_status_id' => 'Status ID',
            'inv_sum' => 'Sum',
            'inv_client_sum' => 'Client Sum',
            'inv_client_currency' => 'Client Currency',
            'inv_currency_rate' => 'Currency Rate',
            'inv_description' => 'Description',
            'inv_created_user_id' => 'Created User ID',
            'inv_updated_user_id' => 'Updated User ID',
            'inv_created_dt' => 'Created Dt',
            'inv_updated_dt' => 'Updated Dt',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['inv_created_dt', 'inv_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['inv_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'inv_created_user_id',
                'updatedByAttribute' => 'inv_updated_user_id',
            ],
        ];
    }

    /**
     * Invoice init create
     */
    public function initCreate(): void
    {
        $this->inv_gid = self::generateGid();
        $this->inv_uid = self::generateUid();
        $this->inv_status_id = self::STATUS_NOT_PAID;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvClientCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'inv_client_currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'inv_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvOrder()
    {
        return $this->hasOne(Order::class, ['or_id' => 'inv_order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'inv_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return InvoiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InvoiceQuery(static::class);
    }

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->inv_status_id] ?? '';
    }

    /**
     * @return string
     */
    public function getStatusClassName(): string
    {
        return self::STATUS_CLASS_LIST[$this->inv_status_id] ?? '';
    }

    /**
     * @return string
     */
    public function getStatusLabel(): string
    {
        return Html::tag('span', $this->getStatusName(), ['class' => 'badge badge-' . $this->getStatusClassName()]);
    }

    /**
     * @return string
     */
    public static function generateGid(): string
    {
        return md5(uniqid('in', true));
    }

    /**
     * @return string
     */
    public static function generateUid(): string
    {
        return uniqid('in');
    }

    /**
     * @return string
     */
    public function generateDescription(): string
    {
        $count = self::find()->where(['inv_order_id' => $this->inv_order_id])->count();
        return 'Invoice ' . ($count + 1);
    }

    /**
     * @return float
     */
    public function calculateClientAmount(): float
    {
        $amount = 0;
        if (is_numeric($this->inv_sum)) {
            if ($this->invClientCurrency && $this->invClientCurrency->cur_app_rate) {
                $this->inv_currency_rate = $this->invClientCurrency->cur_app_rate;
                $amount = $this->inv_sum * $this->inv_currency_rate;
            }
        }
        $this->inv_client_sum = round($amount, 2);
        return $this->inv_client_sum;
    }
}
