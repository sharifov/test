<?php

namespace common\models;

use modules\invoice\src\entities\invoice\Invoice;
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
 *
 * @property Currency $trCurrency
 * @property Invoice $trInvoice
 * @property Payment $trPayment
 */
class Transaction extends \yii\db\ActiveRecord
{
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
}
