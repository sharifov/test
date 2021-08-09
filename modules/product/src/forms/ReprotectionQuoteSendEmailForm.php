<?php

namespace modules\product\src\forms;

use common\models\EmailTemplateType;

class ReprotectionQuoteSendEmailForm extends \yii\base\Model
{
    public $caseId;
    public $quoteId;

    public $clientEmail;
    public $emailTemplateType;

    public function rules(): array
    {
        return [
            [['caseId','quoteId'], 'required'],
            [['caseId','quoteId'], 'integer'],

            [['clientEmail'],'required'],
            [['clientEmail'],'string'],
            [['clientEmail'],'email'],

            ['emailTemplateType', 'required'],
            ['emailTemplateType', 'exist', 'targetAttribute' => 'etp_key', 'targetClass' => EmailTemplateType::class],
        ];
    }
}
