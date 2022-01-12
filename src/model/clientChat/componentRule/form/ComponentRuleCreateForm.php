<?php

namespace src\model\clientChat\componentRule\form;

use src\model\clientChat\componentRule\entity\ClientChatComponentRule;
use yii\helpers\ArrayHelper;

class ComponentRuleCreateForm extends ClientChatComponentRule
{
    public $cccr_runnable_component_changed;

    public function rules(): array
    {
        return ArrayHelper::merge(parent::rules(), [
            ['cccr_runnable_component_changed', 'integer']
        ]);
    }

    public function formName(): string
    {
        return 'ClientChatComponentRule';
    }
}
