<?php

namespace modules\flight\src\forms\api;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use modules\flight\src\services\api\FlightUpdateRequestApiService;
use modules\order\src\entities\order\Order;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class FlightUpdateApiForm
 * @property $type
 * @property $orderUid
 * @property $flights
 * @property $payments
 * @property Order $order
 */
class FlightUpdateRequestApiForm extends Model
{
    public $type;
    public $orderUid;
    public $flights;
    public $payments;
    public $order;

    public function rules(): array
    {
        return [
            [['type'], 'required'],
            [['type'], 'in', 'range' => array_keys(FlightUpdateRequestApiService::TYPE_LIST)],

            [['orderUid'], 'required'],
            [['orderUid'], 'string', 'max' => 255],
            [['orderUid'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['orderUid' => 'or_uid']],
            [['orderUid'], 'setOrder'],

            [['flights'], 'required', 'when' => function () {
                return ArrayHelper::isIn($this->type, [
                    FlightUpdateRequestApiService::TYPE_TICKET_ISSUE,
                    FlightUpdateRequestApiService::TYPE_FLIGHT_REPLACE,
                ]);
            }],
            [['flights'], 'skipOnEmpty' => true, CheckJsonValidator::class],
            [['flights'], 'filter', 'filter' => static function ($value) {
                if (!empty($value)) {
                    return JsonHelper::decode($value);
                }
                return $value;
            }],

            [['payments'], 'required', 'when' => function () {
                return $this->type === FlightUpdateRequestApiService::TYPE_TICKET_ISSUE;
            }],
            [['payments'], 'skipOnEmpty' => true, CheckJsonValidator::class],
            [['payments'], 'filter', 'filter' => static function ($value) {
                if (!empty($value)) {
                    return JsonHelper::decode($value);
                }
                return $value;
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
