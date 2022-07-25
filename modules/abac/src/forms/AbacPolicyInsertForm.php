<?php

namespace modules\abac\src\forms;

use modules\abac\src\AbacService;
use modules\abac\src\entities\AbacPolicy;
use yii\base\Model;

class AbacPolicyInsertForm extends Model
{
    public $ap_subject;
    public $ap_subject_json;
    public $ap_object;
    public $ap_action;
    public $ap_effect;
    public $ap_sort_order;
    public $ap_enabled;

    public function rules()
    {
        return [
            [['ap_subject', 'ap_subject_json', 'ap_object', 'ap_action', 'ap_effect'], 'required'],
            [['ap_effect', 'ap_sort_order'], 'integer'],
            [['ap_enabled'], 'boolean'],
            [['ap_subject_json'], 'validateSubjectJson'],
            [['ap_subject'], 'validateSubject'],
            ['ap_effect', 'in', 'range' => [AbacPolicy::EFFECT_ALLOW, AbacPolicy::EFFECT_DENY]]
        ];
    }

    public function validateSubjectJson($attribute, $params)
    {
        $code = $this->getDecodeCode();
        if (!$code) {
            $this->addError($attribute, 'Invalid ApSubjectJson: "' . $this->{$attribute} . '"');
        }
    }

    public function validateSubject($attribute, $params)
    {
        if (stripos($this->{$attribute}, 'r.sub') === false) {
            $this->addError($attribute, 'Invalid ApSubject: "' . $this->{$attribute} . '"');
        }
    }

    public function getDecodeCode(): string
    {
        $code = '';
        $rules = @json_decode($this->ap_subject_json, true);
        if (is_array($rules)) {
            $code = AbacService::conditionDecode($rules, '');
        }
        return $code;
    }

    public function formName(): string
    {
        return '';
    }
}
