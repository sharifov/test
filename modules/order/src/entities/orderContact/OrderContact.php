<?php

namespace modules\order\src\entities\orderContact;

use common\components\validators\PhoneValidator;
use modules\order\src\entities\order\Order;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "order_contact".
 *
 * @property int $oc_id
 * @property int|null $oc_order_id
 * @property string|null $oc_first_name
 * @property string|null $oc_last_name
 * @property string|null $oc_middle_name
 * @property string|null $oc_email
 * @property string|null $oc_phone_number
 * @property string|null $oc_created_dt
 * @property string|null $oc_updated_dt
 *
 * @property Order $ocOrder
 */
class OrderContact extends \yii\db\ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['oc_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['oc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['oc_created_dt', 'safe'],

            ['oc_email', 'string', 'max' => 100],

            ['oc_first_name', 'string', 'max' => 50],

            ['oc_last_name', 'string', 'max' => 50],

            ['oc_middle_name', 'string', 'max' => 50],

            ['oc_order_id', 'integer'],
            ['oc_order_id', 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['oc_order_id' => 'or_id']],

            ['oc_phone_number', 'string', 'max' => 20],
            ['oc_phone_number', PhoneValidator::class, 'skipOnEmpty' => true],

            ['oc_updated_dt', 'safe'],
        ];
    }

    public function getOcOrder(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Order::class, ['or_id' => 'oc_order_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'oc_id' => 'ID',
            'oc_order_id' => 'Order ID',
            'oc_first_name' => 'First Name',
            'oc_last_name' => 'Last Name',
            'oc_middle_name' => 'Middle Name',
            'oc_email' => 'Email',
            'oc_phone_number' => 'Phone Number',
            'oc_created_dt' => 'Created Dt',
            'oc_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'order_contact';
    }

    public function getFullName()
    {
        return trim(Html::encode($this->oc_first_name . ' ' . $this->oc_last_name . ' ' . $this->oc_last_name));
    }

    public static function create(
        int $orderId,
        string $firstName,
        ?string $lastName,
        ?string $middleName,
        string $email,
        string $phoneNumber
    ): OrderContact {
        $self = new self();
        $self->oc_order_id = $orderId;
        $self->oc_first_name = $firstName;
        $self->oc_last_name = $lastName;
        $self->oc_middle_name = $middleName;
        $self->oc_email = $email;
        $self->oc_phone_number = $phoneNumber;
        return $self;
    }
}
