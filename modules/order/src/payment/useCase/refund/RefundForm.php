<?php

namespace modules\order\src\payment\useCase\refund;

use yii\base\Model;

/**
 * Class RefundForm
 *
 * @property $orderId
 * @property $paymentId
 * @property $comment
 * @property $amount
 */
class RefundForm extends Model
{
    public $orderId;
    public $paymentId;
    public $comment;
    public $amount;

    public function __construct($orderId, $paymentId, $amount, $config = [])
    {
        parent::__construct($config);
        $this->orderId = $orderId;
        $this->paymentId = $paymentId;
        $this->amount = $amount;
    }

    public function rules(): array
    {
        return [
            ['comment', 'required'],
            ['comment', 'string', 'max' => 255],
        ];
    }
}
