<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

class TicketForm extends \yii\base\Model
{
    public $number;

    public $airlinePenalty;

    public $processingFee;

    public $refundAmount;

    public function rules(): array
    {
        return [
            [['number', 'airlinePenalty', 'processingFee', 'refundAmount'], 'required'],
            [['number'], 'string', 'max' => 50],
            [['airlinePenalty', 'processingFee', 'refundAmount'], 'number']
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
