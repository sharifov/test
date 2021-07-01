<?php

namespace sales\model\clientChat\componentEvent\form;

use sales\forms\CompositeForm;
use sales\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use common\components\validators\IsArrayValidator;
use sales\model\clientChat\componentRule\form\ComponentRuleCreateForm;

/**
 * Class ComponentEventCreateForm
 * @package sales\model\clientChat\componentEvent\form
 *
 * @property-read ComponentRuleCreateForm[] $componentRules
 * @property-read ClientChatComponentEvent $componentEvent
 * @property int $pjaxReload
 * @property int $component_event_changed
 */
class ComponentEventCreateForm extends CompositeForm
{
    public $pjaxReload;

    public $component_event_changed;

    public function __construct($countComponentRule = 1, ?ClientChatComponentEvent $model = null, $config = [])
    {
        if (!$model) {
            $this->componentEvent = new ClientChatComponentEvent();
        } else {
            $this->componentEvent = $model;
        }

        $this->componentRules = array_map(static function () {
            $model = new ComponentRuleCreateForm();
            $model->scenario = ComponentRuleCreateForm::SCENARIO_CREATE_WITH_RULE;
            return $model;
        }, self::createCountMultiField($countComponentRule ?: 1));

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['componentEvent', 'safe'],
            ['componentRules', IsArrayValidator::class],
            ['pjaxReload', 'integer'],
            ['pjaxReload', 'default', 'value' => null],
            ['component_event_changed', 'integer']
        ];
    }

    /**
     * @inheritDoc
     */
    protected function internalForms(): array
    {
        return ['componentRules', 'componentEvent'];
    }

    public function componentEventSetDefaultConfig(): void
    {
        if ($this->componentEvent->ccce_component && $this->component_event_changed) {
            $this->componentEvent->ccce_component_config = $this->componentEvent->getComponentClassObject()->getDefaultConfigJson();
        } elseif (!$this->componentEvent->ccce_component) {
            $this->componentEvent->ccce_component_config = null;
        }

        $this->component_event_changed = false;
    }

    public function componentRulesSetDefaultConfig(): void
    {
        foreach ($this->componentRules as $componentRule) {
            if ($componentRule->cccr_runnable_component && $componentRule->cccr_runnable_component_changed) {
                $componentRule->cccr_component_config = $componentRule->getClassObject()->getDefaultConfigJson();
            } elseif (!$componentRule->cccr_runnable_component) {
                $componentRule->cccr_component_config = null;
            }

            $componentRule->cccr_runnable_component_changed = false;
        }
    }
}
