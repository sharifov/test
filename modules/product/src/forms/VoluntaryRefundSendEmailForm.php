<?php

namespace modules\product\src\forms;

use yii\base\Model;

class VoluntaryRefundSendEmailForm extends Model
{
    public $caseId;
    public $originProductQuoteId;
    public $productQuoteRefundId;

    public $clientEmail;

    public function rules(): array
    {
        return [
            [['caseId','originProductQuoteId', 'productQuoteRefundId'], 'required'],
            [['caseId','originProductQuoteId', 'productQuoteRefundId'], 'integer'],

            [['clientEmail'],'required'],
            [['clientEmail'],'string'],
            [['clientEmail'],'email'],
        ];
    }
}
