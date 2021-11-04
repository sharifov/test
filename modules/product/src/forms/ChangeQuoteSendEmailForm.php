<?php

namespace modules\product\src\forms;

/**
 * Class ChangeQuoteSendEmailForm
 */
class ChangeQuoteSendEmailForm extends \yii\base\Model
{
    public $caseId;

    public $clientEmail;

    public function rules(): array
    {
        return [
            [['caseId'], 'required'],
            [['caseId'], 'integer'],

            [['clientEmail'],'required'],
            [['clientEmail'],'string'],
            [['clientEmail'],'email'],
        ];
    }
}
