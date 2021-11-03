<?php

namespace modules\flight\src\useCases\voluntaryRefund\manualCreate;

/**
 * Class TicketForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property string $number
 * @property float $airlinePenalty
 * @property float $processingFee
 * @property float $refundable
 * @property float $selling
 * @property string|null $status
 */
class TicketForm extends \yii\base\Model
{
    public $number;

    public $airlinePenalty;

    public $processingFee;

    public $refundable;

    public $selling;

    public $status;

    public function rules(): array
    {
        return [
            [['number', 'airlinePenalty', 'processingFee', 'refundable', 'selling', 'status'], 'required'],
            [['number'], 'string', 'max' => 50],
            [['airlinePenalty', 'processingFee', 'refundable', 'selling'], 'number'],
            [['status'], 'string', 'max' => 20]
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
