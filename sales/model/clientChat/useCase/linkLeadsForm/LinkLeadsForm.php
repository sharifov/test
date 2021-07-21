<?php

namespace sales\model\clientChat\useCase\linkLeadsForm;

use common\components\validators\IsArrayValidator;
use common\models\Lead;
use yii\base\Model;

class LinkLeadsForm extends Model
{
    public string $rid = '';

    public array $leadIds = [];

    public function rules(): array
    {
        return [
            [['rid', 'leadIds'], 'required'],
            ['rid', 'string', 'max' => 150],

            ['leadIds', IsArrayValidator::class],
            ['leadIds', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['leadIds', 'checkIfExists']
        ];
    }

    public function checkIfExists(): bool
    {
        foreach ($this->leadIds as $leadId) {
            if (!Lead::find()->where(['id' => $leadId])->exists()) {
                $this->addError('leadIds', 'Lead id not exist: ' . $leadId);
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
