<?php

namespace modules\flight\src\useCases\voluntaryRefund\manualCreate;

use common\components\validators\CheckIsNumberValidator;

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
 * @property bool $refundAllowed
 */
class TicketForm extends \yii\base\Model
{
    public $number;

    public $airlinePenalty;

    public $processingFee;

    public $refundable;

    public $selling;

    public $status;

    public $refundAllowed;

    public function rules(): array
    {
        return [
            [['refundAllowed'], 'filter', 'filter' => 'boolval'],
            [['number', 'airlinePenalty', 'processingFee', 'refundable', 'selling', 'status', 'refundAllowed'], 'required'],
            [['number'], 'string', 'max' => 50],
            [['airlinePenalty', 'processingFee', 'refundable', 'selling'], 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],
            [['airlinePenalty', 'processingFee', 'refundable', 'selling'], 'number'],
            [['airlinePenalty', 'processingFee', 'refundable', 'selling'], CheckIsNumberValidator::class],
            [['airlinePenalty', 'refundable', 'selling'], 'number', 'min' => 0, 'skipOnError' => true],
            [['status'], 'string', 'max' => 20]
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
