<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\components\validators\CheckIsBooleanValidator;
use common\components\validators\CheckIsNumberValidator;

/**
 * Class TicketForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property string $number
 * @property float $airlinePenalty
 * @property float $processingFee
 * @property float $refundable
 * @property float $sellingPrice
 * @property float $selling
 * @property bool $refundAllowed
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

    public $refundAllowed;

    public function rules(): array
    {
        return [
            [['number', 'airlinePenalty', 'processingFee', 'refundable', 'selling', 'status', 'refundAllowed'], 'required'],
            [['number'], 'string', 'max' => 50],
            [['airlinePenalty', 'processingFee', 'refundable', 'selling'], CheckIsNumberValidator::class, 'allowInt' => true],
            [['status'], 'string', 'max' => 20],
            [['refundAllowed'], CheckIsBooleanValidator::class]
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
