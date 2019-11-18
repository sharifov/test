<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
 * @property Employee $invUpdatedUser
 */
class Invoice extends \yii\db\ActiveRecord
{
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
        return new InvoiceQuery(get_called_class());
    }
}
