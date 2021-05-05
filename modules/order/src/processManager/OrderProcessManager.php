<?php

namespace modules\order\src\processManager;

use modules\order\src\entities\order\Order;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class OrderProcessManager
 *
 * @property $opm_id
 * @property $opm_status
 * @property $opm_type
 * @property $opm_created_dt
 *
 * @property Order $order
 */
class OrderProcessManager extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%order_process_manager}}';
    }

    public function attributeLabels(): array
    {
        return [
            'opm_id' => 'ID',
            'opm_status' => 'Status',
            'opm_type' => 'Type',
            'opm_created_dt' => 'Created',
        ];
    }

    public function rules(): array
    {
        return [
            ['opm_id', 'integer'],
            ['opm_id', 'exist', 'skipOnEmpty' => true, 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['opm_id' => 'or_id']],

            ['opm_status', 'required'],
            ['opm_status', 'integer'],
            ['opm_status', 'in', 'range' => array_keys(Status::LIST)],

            ['opm_type', 'required'],
            ['opm_type', 'integer'],
            ['opm_type', 'in', 'range' => array_keys(Type::LIST)],

            ['opm_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['or_id' => 'opm_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
