<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\models\Currency;

class VoluntaryRefundForm extends \yii\base\Model
{
    public $processingFee;

    public $penaltyAmount;

    public $totalRefundAmount;

    public $currency;

    public function rules(): array
    {
        return [
            [['processingFee', 'penaltyAmount', 'totalRefundAmount', 'currency'], 'required'],
            [['processingFee', 'penaltyAmount', 'totalRefundAmount'], 'number'],
            ['currency', 'string', 'max' => 3],
            ['currency', 'exist', 'targetClass' => Currency::class, 'targetAttribute' => 'cur_code']
        ];
    }

    public function formName(): string
    {
        return 'refund';
    }
}
