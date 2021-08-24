<?php

namespace modules\product\src\forms;

use common\models\EmailTemplateType;

class ReprotectionQuoteSendEmailForm extends \yii\base\Model
{
    public $caseId;
    public $quoteId;

    public $clientEmail;

    public function rules(): array
    {
        return [
            [['caseId','quoteId'], 'required'],
            [['caseId','quoteId'], 'integer'],

            [['clientEmail'],'required'],
            [['clientEmail'],'string'],
            [['clientEmail'],'email'],
        ];
    }
}
