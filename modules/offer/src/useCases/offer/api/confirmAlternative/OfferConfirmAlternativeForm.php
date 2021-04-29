<?php

namespace modules\offer\src\useCases\offer\api\confirmAlternative;

class OfferConfirmAlternativeForm extends \yii\base\Model
{
    public string $gid;

    public function rules(): array
    {
        return [
            [['gid'], 'required'],
            [['gid'], 'string', 'max' => 32],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}
