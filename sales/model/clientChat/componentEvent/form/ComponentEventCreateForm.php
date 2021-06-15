<?php

namespace sales\model\clientChat\componentEvent\form;

use sales\forms\CompositeForm;
use sales\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use sales\model\clientChat\componentRule\entity\ClientChatComponentRule;

/**
 * Class ComponentEventCreateForm
 * @package sales\model\clientChat\componentEvent\form
 *
 * @property-read ClientChatComponentRule[] $componentRules
 * @property-read ClientChatComponentEvent $componentEvent
 */
class ComponentEventCreateForm extends CompositeForm
{
    public $id;

    public $chatChannelId;

    public $component;

    public $eventType;

    public $componentConfig;

    public $enabled;

    public $sortOrder;

    public function __construct($countComponentRule = 1, $config = [])
    {
        $this->componentEvent = new ClientChatComponentEvent();

        $this->componentRules = array_map(static function () {
            $model = new ClientChatComponentRule();
            $model->scenario = ClientChatComponentRule::SCENARIO_CREATE_WITH_RULE;
            return $model;
        }, self::createCountMultiField($countComponentRule ?: 1));

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['componentEvent', 'safe'],
            ['componentRules', 'common\components\validators\IsArrayValidator']
        ];
    }

    /**
     * @inheritDoc
     */
    protected function internalForms(): array
    {
        return ['componentRules', 'componentEvent'];
    }
}
