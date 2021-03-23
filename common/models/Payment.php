<?php

namespace common\models;

use modules\invoice\src\entities\invoice\Invoice;
use modules\order\src\entities\order\Order;
use modules\order\src\payment\events\PaymentCompletedEvent;
use modules\order\src\payment\events\PaymentRefundedEvent;
use sales\entities\EventTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "payment".
 *
 * @property int $pay_id
 * @property int|null $pay_type_id
 * @property int|null $pay_method_id
 * @property int|null $pay_status_id
 * @property string $pay_date
 * @property float $pay_amount
 * @property string|null $pay_currency
 * @property int|null $pay_invoice_id
 * @property int|null $pay_order_id
 * @property int|null $pay_created_user_id
 * @property int|null $pay_updated_user_id
 * @property string|null $pay_created_dt
 * @property string|null $pay_updated_dt
 * @property string|null $pay_code
 *
 * @property Employee $payCreatedUser
 * @property Currency $payCurrency
 * @property Invoice $payInvoice
 * @property PaymentMethod $payMethod
 * @property Order $payOrder
 * @property Employee $payUpdatedUser
 * @property Transaction[] $transactions
 */
class Payment extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const STATUS_NEW = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_IN_PROGRESS = 3;
    public const STATUS_AUTHORIZED = 4;
    public const STATUS_CAPTURED = 5;
    public const STATUS_REFUNDED = 6;
    public const STATUS_CANCELED = 7;
    public const STATUS_FAILED = 8;
    public const STATUS_DECLINED = 9;
    public const STATUS_COMPLETED = 10;

    public const STATUS_LIST = [
        self::STATUS_NEW => 'New',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_AUTHORIZED => 'Authorized',
        self::STATUS_CAPTURED => 'Captured',
        self::STATUS_REFUNDED => 'Refunded',
        self::STATUS_CANCELED => 'Canceled',
        self::STATUS_FAILED => 'Failed',
        self::STATUS_DECLINED => 'Declined',
        self::STATUS_COMPLETED => 'Completed',
    ];

    public function isCompleted(): bool
    {
        return $this->pay_status_id === self::STATUS_COMPLETED;
    }

    public function completed(): void
    {
        if ($this->isCompleted()) {
            throw new \DomainException('Payment is already completed.');
        }
        $this->pay_status_id = self::STATUS_COMPLETED;
        $this->recordEvent(new PaymentCompletedEvent($this->pay_id));
    }

    public function isRefunded(): bool
    {
        return $this->pay_status_id === self::STATUS_REFUNDED;
    }

    public function refund(): void
    {
        if ($this->isRefunded()) {
            throw new \DomainException('Payment is already refund.');
        }
        $this->pay_status_id = self::STATUS_REFUNDED;
        $this->recordEvent(new PaymentRefundedEvent($this->pay_id));
    }

    public function inProgress(): void
    {
        $this->pay_status_id = self::STATUS_IN_PROGRESS;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pay_type_id', 'pay_method_id', 'pay_status_id', 'pay_invoice_id', 'pay_order_id', 'pay_created_user_id', 'pay_updated_user_id'], 'integer'],
            [['pay_date', 'pay_amount'], 'required'],
            ['pay_date', 'date', 'format' => 'php:Y-m-d'],
            [['pay_created_dt', 'pay_updated_dt'], 'safe'],
            [['pay_amount'], 'number'],
            [['pay_code'], 'string'],
            [['pay_currency'], 'default', 'value' => null],
            [['pay_currency'], 'string', 'max' => 3],
            [['pay_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pay_created_user_id' => 'id']],
            [['pay_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['pay_currency' => 'cur_code']],
            [['pay_invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::class, 'targetAttribute' => ['pay_invoice_id' => 'inv_id']],
            [['pay_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentMethod::class, 'targetAttribute' => ['pay_method_id' => 'pm_id']],
            [['pay_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['pay_order_id' => 'or_id']],
            [['pay_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pay_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pay_id' => 'ID',
            'pay_type_id' => 'Type ID',
            'pay_method_id' => 'Method ID',
            'pay_status_id' => 'Status ID',
            'pay_date' => 'Date',
            'pay_amount' => 'Amount',
            'pay_currency' => 'Currency',
            'pay_invoice_id' => 'Invoice ID',
            'pay_order_id' => 'Order ID',
            'pay_created_user_id' => 'Created User ID',
            'pay_updated_user_id' => 'Updated User ID',
            'pay_created_dt' => 'Created Dt',
            'pay_updated_dt' => 'Updated Dt',
            'pay_code' => 'Code',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pay_created_dt', 'pay_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pay_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
//            'user' => [
//                'class' => BlameableBehavior::class,
//                'createdByAttribute' => 'pay_created_user_id',
//                'updatedByAttribute' => 'pay_updated_user_id',
//            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pay_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'pay_currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayInvoice()
    {
        return $this->hasOne(Invoice::class, ['inv_id' => 'pay_invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayMethod()
    {
        return $this->hasOne(PaymentMethod::class, ['pm_id' => 'pay_method_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayOrder()
    {
        return $this->hasOne(Order::class, ['or_id' => 'pay_order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pay_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['tr_payment_id' => 'pay_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\PaymentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\PaymentQuery(static::class);
    }

    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    public static function getStatusName(?int $status): ?string
    {
        return self::getStatusList()[$status] ?? null;
    }

    public static function create(
        int $methodId,
        string $date,
        float $amount,
        string $currency,
        int $invoiceId,
        int $orderId,
        string $code
    ): self {
        $payment = new self();
        $payment->pay_method_id = $methodId;
        $payment->pay_date = $date;
        $payment->pay_amount = $amount;
        $payment->pay_currency = $currency;
        $payment->pay_invoice_id = $invoiceId;
        $payment->pay_order_id = $orderId;
        $payment->pay_code = $code;
        return $payment;
    }
}
