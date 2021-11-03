<?php

namespace modules\flight\src\useCases\voluntaryRefund\manualCreate;

use common\components\validators\CheckIsNumberValidator;
use common\components\validators\CheckJsonValidator;
use common\components\validators\IsArrayValidator;
use common\components\validators\CheckIsBooleanValidator;
use yii\helpers\Json;

/**
 * Class AuxiliaryOptionForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property string $type
 * @property float $amount
 * @property float $refundable
 * @property string $status
 * @property bool $refundAllow
 * @property string $details
 */
class AuxiliaryOptionForm extends \yii\base\Model
{
    public $type;

    public $amount;

    public $refundable;

    public $status;

    public $refundAllow;

    public $details;

    public function rules(): array
    {
        return [
            [['type', 'amount', 'refundable', 'status', 'refundAllow'], 'required'],
            [['type', 'status'], 'string', 'max' => 50],
            [['refundAllow'], CheckIsBooleanValidator::class],
            [['refundable', 'amount'], CheckIsNumberValidator::class, 'allowInt' => true],
            [['details'], CheckJsonValidator::class]
        ];
    }

    public function formName(): string
    {
        return '';
    }
}