<?php

namespace modules\flight\src\forms\api;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use modules\order\src\entities\order\Order;
use yii\base\Model;

/**
 * Class FlightTicketIssueRequestApiForm

 * @property $orderUid
 * @property $flights
 * @property $payments
 * @property Order $order
 */
class FlightTicketIssueRequestApiForm extends Model
{
    public $orderUid;
    public $flights;
    public $payments;
    public $order;

    public function rules(): array
    {
        return [
            [['orderUid'], 'required'],
            [['orderUid'], 'string', 'max' => 15],
            [['orderUid'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['orderUid' => 'or_uid']],
            [['orderUid'], 'setOrder'],

            [['flights'], 'required'],
            [['flights'], CheckJsonValidator::class],
            [['flights'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],

            [['payments'], 'required'],
            [['payments'], CheckJsonValidator::class],
            [['payments'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
        ];
    }

    public function setOrder()
    {
        $this->order = Order::findOne(['or_uid' => $this->orderUid]);
    }

    public function formName(): string
    {
        return '';
    }
}
