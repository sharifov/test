<?php

namespace modules\order\src\entities\orderData;

use common\models\Employee;
use modules\order\src\entities\order\Order;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_data".
 *
 * @property int $od_id
 * @property int $od_order_id
 * @property string|null $od_display_uid
 * @property string|null $od_source_cid
 * @property int|null $od_created_by
 * @property int|null $od_updated_by
 * @property string|null $od_created_dt
 * @property string|null $od_updated_dt
 *
 * @property Employee $odCreatedBy
 * @property Order $odOrder
 * @property Employee $odUpdatedBy
 */
class OrderData extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['od_created_dt', 'od_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['od_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'od_created_by',
                'updatedByAttribute' => 'od_updated_dt',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['od_created_by', 'integer'],
            ['od_created_by', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['od_created_by' => 'id']],

            ['od_created_dt', 'safe'],

            ['od_display_uid', 'string', 'max' => 10],

            ['od_order_id', 'required'],
            ['od_order_id', 'integer'],
            ['od_order_id', 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['od_order_id' => 'or_id']],

            ['od_source_cid', 'string', 'max' => 10],

            ['od_updated_by', 'integer'],
            ['od_updated_by', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['od_updated_by' => 'id']],

            ['od_updated_dt', 'safe'],
        ];
    }

    public function getOdCreatedBy(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'od_created_by']);
    }

    public function getOdOrder(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Order::class, ['or_id' => 'od_order_id']);
    }

    public function getOdUpdatedBy(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'od_updated_by']);
    }

    public function attributeLabels(): array
    {
        return [
            'od_id' => 'ID',
            'od_order_id' => 'Order ID',
            'od_display_uid' => 'Display Uid',
            'od_source_cid' => 'Source Cid',
            'od_created_by' => 'Created By',
            'od_updated_by' => 'Updated By',
            'od_created_dt' => 'Created Dt',
            'od_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'order_data';
    }

    public static function create(int $orderId, string $displayUid, string $sourceId): self
    {
        $data = new self();
        $data->od_order_id = $orderId;
        $data->od_display_uid = $displayUid;
        $data->od_source_cid = $sourceId;
        return $data;
    }
}
