<?php

namespace common\models;

use modules\invoice\src\entities\invoice\Invoice;
use sales\entities\EventTrait;
use Yii;

/**
 * This is the model class for table "transaction".
 *
 * @property int $tr_id
 * @property string|null $tr_code
 * @property int|null $tr_invoice_id
 * @property int|null $tr_payment_id
 * @property int|null $tr_type_id
 * @property string $tr_date
 * @property float $tr_amount
 * @property string|null $tr_currency
 * @property string|null $tr_created_dt
 * @property string|null $tr_comment
 *
 * @property Currency $trCurrency
 * @property Invoice $trInvoice
 * @property Payment $trPayment
 */
class Transaction extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const TYPE_AUTHORIZATION = 1;
    public const TYPE_CAPTURE = 2;
    public const TYPE_REFUND = 3;
    public const TYPE_VOID = 4;

    public const TYPE_LIST = [
        self::TYPE_AUTHORIZATION => 'Authorized',
        self::TYPE_CAPTURE => 'Capture',
        self::TYPE_REFUND => 'Refund',
        self::TYPE_VOID => 'Void',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tr_invoice_id', 'tr_payment_id', 'tr_type_id'], 'integer'],
            [['tr_date', 'tr_amount'], 'required'],
            [['tr_date', 'tr_created_dt'], 'safe'],
            [['tr_amount'], 'number'],
            [['tr_code'], 'string', 'max' => 30],
            [['tr_currency'], 'string', 'max' => 3],
            [['tr_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['tr_currency' => 'cur_code']],
            [['tr_invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::class, 'targetAttribute' => ['tr_invoice_id' => 'inv_id']],
            [['tr_payment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payment::class, 'targetAttribute' => ['tr_payment_id' => 'pay_id']],

            ['tr_comment', 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tr_id' => 'ID',
            'tr_code' => 'Code',
            'tr_invoice_id' => 'Invoice ID',
            'tr_payment_id' => 'Payment ID',
            'tr_type_id' => 'Type ID',
            'tr_date' => 'Date',
            'tr_amount' => 'Amount',
            'tr_currency' => 'Currency',
            'tr_created_dt' => 'Created Dt',
            'tr_comment' => 'Comment',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'tr_currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrInvoice()
    {
        return $this->hasOne(Invoice::class, ['inv_id' => 'tr_invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrPayment()
    {
        return $this->hasOne(Payment::class, ['pay_id' => 'tr_payment_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\query\TransactionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\TransactionQuery(static::class);
    }

    public static function getTypeList(): array
    {
        return self::TYPE_LIST;
    }

    public static function getTypeName(?int $type): ?string
    {
        return self::getTypeList()[$type] ?? null;
    }

    public static function create(
        float $amount,
        string $code,
        string $date,
        int $paymentId,
        int $typeId,
        string $currency,
        ?string $comment
    ): Transaction {
        $model = new self();
        $model->tr_amount = $amount;
        $model->tr_code = $code;
        $model->tr_date = $date;
        $model->tr_payment_id = $paymentId;
        $model->tr_comment = $comment;
        $model->tr_type_id = $typeId;
        $model->tr_currency = $currency;
        $model->tr_created_dt = date('Y-m-d H:i:s');
        return $model;
    }
}
