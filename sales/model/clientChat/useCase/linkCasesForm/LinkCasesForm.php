<?php

namespace sales\model\clientChat\useCase\linkCasesForm;

use common\components\validators\IsArrayValidator;
use sales\entities\cases\Cases;
use yii\base\Model;

class LinkCasesForm extends Model
{
    public string $rid = '';

    public array $caseIds = [];

    public function rules(): array
    {
        return [
            [['rid', 'caseIds'], 'required'],
            ['rid', 'string', 'max' => 150],

            ['caseIds', IsArrayValidator::class],
            ['caseIds', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['caseIds', 'checkIfExists']
        ];
    }

    public function checkIfExists(): bool
    {
        foreach ($this->caseIds as $caseId) {
            if (!Cases::find()->where(['cs_id' => $caseId])->exists()) {
                $this->addError('caseIds', 'Case id not exist: ' . $caseId);
                return false;
            }
        }
        return true;
    }

    public function formName(): string
    {
        return '';
    }
}
