<?php

namespace modules\order\src\entities\orderRefund;

use common\models\Currency;
use common\models\Employee;
use common\models\query\CurrencyQuery;
use common\models\query\EmployeeQuery;
use modules\order\src\entities\order\Order;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_refund".
 *
 * @property int $orr_id
 * @property string|null $orr_uid
 * @property int $orr_order_id
 * @property float|null $orr_selling_price
 * @property float|null $orr_penalty_amount
 * @property float|null $orr_processing_fee_amount
 * @property float|null $orr_charge_amount
 * @property float|null $orr_refund_amount
 * @property int|null $orr_client_status_id
 * @property int|null $orr_status_id
 * @property string|null $orr_client_currency
 * @property float|null $orr_client_currency_rate
 * @property float|null $orr_client_selling_price
 * @property float|null $orr_client_charge_amount
 * @property float|null $orr_client_refund_amount
 * @property string|null $orr_description
 * @property string|null $orr_expiration_dt
 * @property int|null $orr_created_user_id
 * @property int|null $orr_updated_user_id
 * @property string|null $orr_created_dt
 * @property string|null $orr_updated_dt
 *
 * @property Currency $clientCurrency
 * @property Employee $createdUser
 * @property Order $order
 * @property Employee $updatedUser
 */
class OrderRefund extends \yii\db\ActiveRecord
{
    public static function createByScheduleChange(): self
    {
        $refund = new self();
        $refund->orr_processing_fee_amount = 0;
        $refund->orr_penalty_amount = 0;
        $refund->orr_charge_amount = 0;
        $refund->orr_description = 'Schedule change refund request';
        //$refund->orr_status_id = OrderRefundStatus::PE;
        return $refund;
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['orr_created_dt', 'orr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['orr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'orr_created_user_id',
                'updatedByAttribute' => 'orr_updated_user_id',
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'order_refund';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['orr_order_id', 'orr_uid'], 'required'],
            [['orr_order_id', 'orr_client_status_id', 'orr_status_id', 'orr_created_user_id', 'orr_updated_user_id'], 'integer'],
            [['orr_selling_price', 'orr_penalty_amount', 'orr_processing_fee_amount', 'orr_charge_amount', 'orr_refund_amount', 'orr_client_currency_rate', 'orr_client_selling_price', 'orr_client_charge_amount', 'orr_client_refund_amount'], 'number', 'min' => 0, 'max' => 999999.99],
            [['orr_description'], 'string'],
            [['orr_expiration_dt', 'orr_created_dt', 'orr_updated_dt'], 'safe'],

            [['orr_uid'], 'string', 'max' => 15],
            [['orr_client_currency'], 'string', 'max' => 3],
            [['orr_client_currency'], 'default', 'value' => null],

            [['orr_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['orr_client_currency' => 'cur_code']],
            [['orr_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['orr_created_user_id' => 'id']],
            [['orr_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['orr_order_id' => 'or_id']],
            [['orr_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['orr_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'orr_id' => 'ID',
            'orr_uid' => 'Uid',
            'orr_order_id' => 'Order ID',
            'orr_selling_price' => 'Selling Price',
            'orr_penalty_amount' => 'Penalty Amount',
            'orr_processing_fee_amount' => 'Processing Fee Amount',
            'orr_charge_amount' => 'Charge Amount',
            'orr_refund_amount' => 'Refund Amount',
            'orr_client_status_id' => 'Client Status ID',
            'orr_status_id' => 'Status ID',
            'orr_client_currency' => 'Client Currency',
            'orr_client_currency_rate' => 'Client Currency Rate',
            'orr_client_selling_price' => 'Client Selling Price',
            'orr_client_charge_amount' => 'Client Charge Amount',
            'orr_client_refund_amount' => 'Client Refund Amount',
            'orr_description' => 'Description',
            'orr_expiration_dt' => 'Expiration Dt',
            'orr_created_user_id' => 'Created User ID',
            'orr_updated_user_id' => 'Updated User ID',
            'orr_created_dt' => 'Created Dt',
            'orr_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[OrrClientCurrency]].
     *
     * @return \yii\db\ActiveQuery|CurrencyQuery
     */
    public function getClientCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'orr_client_currency']);
    }

    /**
     * Gets query for [[OrrCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'orr_created_user_id']);
    }

    /**
     * Gets query for [[OrrOrder]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Order::class, ['or_id' => 'orr_order_id']);
    }

    /**
     * Gets query for [[OrrUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'orr_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find(): Scopes
    {
        return new Scopes(get_called_class());
    }

    /**
     * @return string
     */
    public static function generateUid(): string
    {
        return uniqid('or');
    }
}
