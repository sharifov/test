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

 * @property $orderUid
 * @property $flights
 * @property $payments
 * @property Order $order
 */
class FlightUpdateRequestApiForm extends Model
{
    public $orderUid;
    public $flights;
    public $payments;
    public $order;

    public function rules(): array
    {
        return [
            [['orderUid'], 'required'],
            [['orderUid'], 'string', 'max' => 255],
            [['orderUid'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['orderUid' => 'or_gid']],
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

            [['trips'], 'required'],
            [['trips'], CheckJsonValidator::class],
            [['trips'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
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
