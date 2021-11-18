<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\components\validators\CheckIsBooleanValidator;

/**
 * Class TicketForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property string $number
 * @property float $airlinePenalty
 * @property float $processingFee
 * @property float $refundAmount
 * @property float $sellingPrice
 * @property bool $refundAllowed
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

    public $refundAllowed;

    public function rules(): array
    {
        return [
            [['number', 'airlinePenalty', 'processingFee', 'refundAmount', 'sellingPrice', 'status', 'refundAllowed'], 'required'],
            [['number'], 'string', 'max' => 50],
            [['airlinePenalty', 'processingFee', 'refundAmount', 'sellingPrice'], 'number'],
            [['status'], 'string', 'max' => 20],
            [['refundAllowed'], CheckIsBooleanValidator::class]
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
