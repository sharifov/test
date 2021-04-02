<?php

namespace modules\flight\src\forms;

use frontend\helpers\JsonHelper;
use yii\base\Model;

/**
 * Class TicketFlightsForm
 * @property $uniqueId
 * @property $status
 * @property $payment
 */
class TicketFlightsForm extends Model
{
    public $uniqueId;
    public $status;
    public $payment;

    public function rules(): array
    {
        return [
            ['uniqueId', 'required'],
            ['uniqueId', 'trim'],
            ['uniqueId', 'string', 'max' => 100],

            ['status', 'required'],
            ['status', 'integer'],
            ['status', 'filter', 'filter' => 'intval', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['payment', 'safe'],
            ['payment', 'preparePayment'],
        ];
    }

    public function preparePayment($attribute): void
    {
        if (!empty($this->payment)) {
            $this->payment = JsonHelper::decode($this->payment);
        }
    }

    public function formName(): string
    {
        return '';
    }
}
