<?php

namespace modules\invoice\src\entities\invoice;

use common\models\Currency;
use common\models\Employee;
use modules\order\src\entities\order\Order;
use sales\entities\EventTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
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
class Invoice extends ActiveRecord
{
    use EventTrait;

    public static function tableName(): string
    {
        return 'invoice';
    }

    public function rules(): array
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

    public function attributeLabels(): array
    {
        return [
            'inv_id' => 'ID',
            'inv_gid' => 'GID',
            'inv_uid' => 'UID',
            'inv_order_id' => 'Order ID',
            'invOrder' => 'Order',
            'inv_status_id' => 'Status',
            'inv_sum' => 'Sum',
            'inv_client_sum' => 'Client Sum',
            'inv_client_currency' => 'Client Currency',
            'inv_currency_rate' => 'Currency Rate',
            'inv_description' => 'Description',
            'inv_created_user_id' => 'Created User',
            'invCreatedUser' => 'Created User',
            'inv_updated_user_id' => 'Updated User',
            'invUpdatedUser' => 'Updated User',
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
        $this->inv_status_id = InvoiceStatus::NOT_PAID;
    }

    /**
     * @return ActiveQuery
     */
    public function getInvClientCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'inv_client_currency']);
    }

    /**
     * @return ActiveQuery
     */
    public function getInvCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'inv_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getInvOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['or_id' => 'inv_order_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getInvUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'inv_updated_user_id']);
    }

    public static function find()
    {
        return new Scopes(static::class);
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
