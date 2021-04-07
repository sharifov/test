<?php

namespace sales\model\leadOrder\entity;

use common\models\Employee;
use common\models\Lead;
use modules\order\src\entities\order\Order;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_order".
 *
 * @property int $lo_order_id
 * @property int $lo_lead_id
 * @property string|null $lo_create_dt
 * @property int|null $lo_created_user_id
 *
 * @property Employee $createdUser
 * @property Lead $lead
 * @property Order $order
 */
class LeadOrder extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lo_create_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lo_created_user_id'],
                ]
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['lo_order_id', 'lo_lead_id'], 'unique', 'targetAttribute' => ['lo_order_id', 'lo_lead_id']],

            ['lo_create_dt', 'safe'],

            ['lo_created_user_id', 'integer'],
            ['lo_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lo_created_user_id' => 'id']],

            ['lo_lead_id', 'required'],
            ['lo_lead_id', 'integer'],
            ['lo_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lo_lead_id' => 'id']],

            ['lo_order_id', 'required'],
            ['lo_order_id', 'integer'],
            ['lo_order_id', 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['lo_order_id' => 'or_id']],
        ];
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'lo_created_user_id']);
    }

    public function getLead(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'lo_lead_id']);
    }

    public function getOrder(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Order::class, ['or_id' => 'lo_order_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'lo_order_id' => 'Order ID',
            'lo_lead_id' => 'Lead ID',
            'lo_create_dt' => 'Create Dt',
            'lo_created_user_id' => 'Created User ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_order';
    }
}
