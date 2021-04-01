<?php

namespace modules\flight\src\forms\api;

use modules\order\src\entities\order\Order;
use yii\base\Model;

/**
 * Class FlightFailRequestApiForm
 * @property $orderUid
 * @property $description
 * @property Order $order
 */
class FlightFailRequestApiForm extends Model
{
    public $orderUid;
    public $order;
    public $description;

    public function rules(): array
    {
        return [
            [['orderUid'], 'required'],
            [['orderUid'], 'string', 'max' => 15],
            [['orderUid'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['orderUid' => 'or_gid']],
            [['orderUid'], 'setOrder'],

            [['description'], 'string', 'max' => 100],
        ];
    }

    public function setOrder()
    {
        $this->order = Order::findOne(['or_gid' => $this->orderUid]);
    }

    public function formName(): string
    {
        return '';
    }
}
