<?php

namespace sales\model\caseOrder\entity;

use common\models\Employee;
use modules\order\src\entities\order\Order;
use sales\entities\cases\Cases;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "case_order".
 *
 * @property int $co_order_id
 * @property int $co_case_id
 * @property string|null $co_create_dt
 * @property int|null $co_created_user_id
 *
 * @property Cases $cases
 * @property Order $order
 * @property Employee $createdUser
 */
class CaseOrder extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['co_create_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['co_created_user_id'],
                ]
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['co_order_id', 'co_case_id'], 'unique', 'targetAttribute' => ['co_order_id', 'co_case_id']],

            ['co_case_id', 'required'],
            ['co_case_id', 'integer'],
            ['co_case_id', 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['co_case_id' => 'cs_id']],

            ['co_create_dt', 'safe'],

            ['co_created_user_id', 'integer'],
            ['co_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['co_created_user_id' => 'id']],

            ['co_order_id', 'required'],
            ['co_order_id', 'integer'],
            ['co_order_id', 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['co_order_id' => 'or_id']],
        ];
    }

    public function getCases(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'co_case_id']);
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'co_created_user_id']);
    }

    public function getOrder(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Order::class, ['or_id' => 'co_order_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'co_order_id' => 'Order ID',
            'co_case_id' => 'Case ID',
            'co_create_dt' => 'Create Dt',
            'co_created_user_id' => 'Created User ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'case_order';
    }

    public static function create(int $caseId, int $orderId): CaseOrder
    {
        $rel = new self();
        $rel->co_case_id = $caseId;
        $rel->co_order_id = $orderId;
        return $rel;
    }
}
