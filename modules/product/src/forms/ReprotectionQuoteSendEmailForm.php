<?php

namespace modules\product\src\forms;

use common\models\EmailTemplateType;

class ReprotectionQuoteSendEmailForm extends \yii\base\Model
{
    public $caseId;
    public $quoteId;
    public $orderId;
    public $pqcId;

    public $clientEmail;

    public function rules(): array
    {
        return [
            [['caseId','quoteId', 'orderId', 'pqcId'], 'required'],
            [['caseId','quoteId', 'orderId', 'pqcId'], 'integer'],

            [['clientEmail'],'required'],
            [['clientEmail'],'string'],
            [['clientEmail'],'email'],
        ];
    }
}
