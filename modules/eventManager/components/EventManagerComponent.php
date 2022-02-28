<?php

namespace modules\eventManager\components;

use yii\base\Component;

class EventManagerComponent extends Component
{
    public array $objectList = [];
    public bool $cacheEnable = true;
    public string $cacheKey = 'event-manager-list';
    public string $cacheTagDependency = 'event-manager-tag-dependency';

    public function init(): void
    {
        parent::init();
        $policyListContent = $this->getPolicyListContent();
    }

    /**
     * @return array
     */
    public function getObjectList(): array
    {
        return $this->objectList;
    }
}
