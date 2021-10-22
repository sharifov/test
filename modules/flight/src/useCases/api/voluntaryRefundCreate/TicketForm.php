<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

/**
 * Class TicketForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property string $number
 * @property float $airlinePenalty
 * @property float $processingFee
 * @property float $refundAmount
 * @property float $sellingPrice
 * @property string|null $status
 */
class TicketForm extends \yii\base\Model
{
    public $number;

    public $airlinePenalty;

    public $processingFee;

    public $refundAmount;

    public $sellingPrice;

    public $status;

    public function rules(): array
    {
        return [
            [['number', 'airlinePenalty', 'processingFee', 'refundAmount', 'sellingPrice'], 'required'],
            [['number'], 'string', 'max' => 50],
            [['airlinePenalty', 'processingFee', 'refundAmount', 'sellingPrice'], 'number'],
            [['status'], 'string', 'max' => 20]
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
