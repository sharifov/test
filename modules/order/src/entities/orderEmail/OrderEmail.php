<?php

namespace modules\order\src\entities\orderEmail;

use common\models\Email;
use modules\order\src\entities\order\Order;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_email".
 *
 * @property int $oe_id
 * @property int|null $oe_order_id
 * @property int|null $oe_email_id
 * @property string|null $oe_created_dt
 *
 * @property Email $email
 * @property Order $order
 */
class OrderEmail extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['oe_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ]
        ];
    }

    public function rules(): array
    {
        return [
            ['oe_created_dt', 'safe'],

            ['oe_email_id', 'integer'],
            ['oe_email_id', 'exist', 'skipOnError' => true, 'targetClass' => Email::class, 'targetAttribute' => ['oe_email_id' => 'e_id']],

            ['oe_order_id', 'integer'],
            ['oe_order_id', 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['oe_order_id' => 'or_id']],
        ];
    }

    public function getEmail(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Email::class, ['e_id' => 'oe_email_id']);
    }

    public function getOrder(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Order::class, ['or_id' => 'oe_order_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'oe_id' => 'ID',
            'oe_order_id' => 'Order ID',
            'oe_email_id' => 'Email ID',
            'oe_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'order_email';
    }
}
