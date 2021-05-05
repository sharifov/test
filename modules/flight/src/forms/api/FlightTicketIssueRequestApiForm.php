<?php

namespace modules\flight\src\forms\api;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use modules\order\src\entities\order\Order;
use yii\base\Model;

/**
 * Class FlightTicketIssueRequestApiForm

 * @property $fareId
 * @property $flights
 * @property Order $order
 */
class FlightTicketIssueRequestApiForm extends Model
{
    public $fareId;
    public $flights;

    private $order;

    public function rules(): array
    {
        return [
            [['fareId'], 'required'],
            [['fareId'], 'string', 'max' => 255],
            [['fareId'], 'detectOrder'],

            [['flights'], 'required'],
            [['flights'], CheckJsonValidator::class],
            [['flights'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
        ];
    }

    public function detectOrder($attribute)
    {
        if (!$this->order = Order::findOne(['or_fare_id' => $this->fareId])) {
            $this->addError($attribute, 'Order not found by fareId(' . $this->fareId . ')');
        }
    }

    public function formName(): string
    {
        return '';
    }
}
