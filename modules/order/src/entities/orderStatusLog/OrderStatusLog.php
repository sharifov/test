<?php

namespace modules\order\src\entities\orderStatusLog;

use common\models\Employee;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\order\OrderStatusAction;
use yii\db\ActiveQuery;

/**
 * @property int $orsl_id
 * @property int $orsl_order_id
 * @property int|null $orsl_start_status_id
 * @property int $orsl_end_status_id
 * @property string $orsl_start_dt
 * @property string|null $orsl_end_dt
 * @property int|null $orsl_duration
 * @property string|null $orsl_description
 * @property int|null $orsl_owner_user_id
 * @property int|null $orsl_created_user_id
 * @property int|null $orsl_action_id
 *
 * @property Employee $createdUser
 * @property Employee $ownerUser
 * @property Order $order
 */
class OrderStatusLog extends \yii\db\ActiveRecord
{
    public static function create(CreateDto $dto): self
    {
        $log = new static();
        $log->orsl_order_id = $dto->orderId;
        $log->orsl_start_status_id = $dto->startStatusId;
        $log->orsl_end_status_id = $dto->endStatusId;
        $log->orsl_start_dt = date('Y-m-d H:i:s');
        $log->orsl_description = $dto->description;
        $log->orsl_action_id = $dto->actionId;
        $log->orsl_owner_user_id = $dto->ownerId;
        $log->orsl_created_user_id = $dto->creatorId;
        return $log;
    }

    public function end(): void
    {
        $this->orsl_end_dt = date('Y-m-d H:i:s');
        $this->orsl_duration = (int) (strtotime($this->orsl_end_dt) - strtotime($this->orsl_start_dt));
    }

    public static function tableName(): string
    {
        return '{{%order_status_log}}';
    }

    public function rules(): array
    {
        return [
            ['orsl_order_id', 'required'],
            ['orsl_order_id', 'integer'],
            ['orsl_order_id', 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['orsl_order_id' => 'or_id']],

            ['orsl_start_status_id', 'integer'],
            ['orsl_start_status_id', 'in', 'range' => array_keys(OrderStatus::getList())],

            ['orsl_end_status_id', 'required'],
            ['orsl_end_status_id', 'integer'],
            ['orsl_end_status_id', 'in', 'range' => array_keys(OrderStatus::getList())],

            ['orsl_start_dt', 'required'],
            ['orsl_start_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['orsl_end_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['orsl_duration', 'integer'],

            ['orsl_description', 'string', 'max' => 255],

            ['orsl_action_id', 'integer'],
            ['orsl_action_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['orsl_action_id', 'in', 'range' => array_keys(OrderStatusAction::getList())],

            ['orsl_owner_user_id', 'integer'],
            ['orsl_owner_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['orsl_owner_user_id' => 'id']],

            ['orsl_created_user_id', 'integer'],
            ['orsl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['orsl_created_user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'orsl_id' => 'ID',
            'orsl_order_id' => 'Order ID',
            'order' => 'Order',
            'orsl_start_status_id' => 'Start Status',
            'orsl_end_status_id' => 'End Status',
            'orsl_start_dt' => 'Start Dt',
            'orsl_end_dt' => 'End Dt',
            'orsl_duration' => 'Duration',
            'orsl_description' => 'Description',
            'orsl_action_id' => 'Action',
            'orsl_owner_user_id' => 'Owner User',
            'orsl_created_user_id' => 'Created User',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'orsl_created_user_id']);
    }

    public function getOwnerUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'orsl_owner_user_id']);
    }

    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['or_id' => 'orsl_order_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
