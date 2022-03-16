<?php

namespace modules\eventManager\components;

use modules\eventManager\src\entities\EventHandler;
use modules\eventManager\src\entities\EventList;
use modules\eventManager\src\services\EventService;
use src\helpers\app\AppHelper;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\caching\TagDependency;
use yii\helpers\VarDumper;

class EventManagerComponent extends Component
{
    public array $objectList = [];
    public array $categoryList = [];
    public bool $cacheEnable = true;
    public string $cacheKey = 'event-manager-list';
    public string $cacheTagDependency = 'event-manager-tag-dependency';

    public function init(): void
    {
        parent::init();
        $eventListContent = $this->getEventList();
        //VarDumper::dump($eventListContent);
    }

    /**
     * @return array
     */
    public function getObjectList(): array
    {
        return $this->objectList;
    }

    /**
     * @return array
     */
    public function getCategoryList(): array
    {
        return $this->objectList;
    }

    /**
     * @return array|EventList[]
     */
    final public function getEventListWOCache(): array
    {
        $rows = EventList::find()->select([
            'el_id',
            'el_key',
            'el_enable_type',
            'el_enable_log',
            'el_break',
            'el_sort_order',
            'el_cron_expression',
            'el_condition',
            'el_params'
        ])
//            ->where(['ap_enabled' => true])
            ->orderBy(['el_sort_order' => SORT_ASC, 'el_id' => SORT_ASC])->asArray()->all();

        return $rows;
    }


    /**
     * @return array|EventList[]
     */
    final public function getEventHandlerListWOCache(): array
    {
        $rows = EventHandler::find()->select([
            'eh_id',
            'eh_el_id',
            'eh_class',
            'eh_method',
            'eh_enable_type',
            'eh_enable_log',
            'eh_asynch',
            'eh_break',
            'eh_cron_expression',
            'eh_condition',
            'eh_params'
        ])
//            ->where(['ap_enabled' => true])
            ->orderBy(['eh_sort_order' => SORT_ASC, 'eh_id' => SORT_ASC])->asArray()->all();

        $data = [];
        if ($rows) {
            foreach ($rows as $item) {
                $data[$item['eh_el_id']][] = $item;
            }
        }
        return $data;
    }


    /**
     * @param int $eventId
     * @param array $handlerList
     * @return array
     */
    public static function getHandlerListByEventId(int $eventId, array $handlerList): array
    {
        $data = empty($handlerList[$eventId]) ? [] : $handlerList[$eventId];
        return $data;
    }

    /**
     * @return array|EventList[]
     */
    private function getCompleteEventListWOCache(): array
    {
        $eventListData = $this->getEventListWOCache();

        if ($eventListData) {
            $handlerListData = $this->getEventHandlerListWOCache();

            if ($handlerListData) {
                foreach ($eventListData as $key => $item) {
                    $eventListData[$key]['handlerList'] =
                        self::getHandlerListByEventId($item['el_id'], $handlerListData);
                }
            }
        }
        return $eventListData;
    }


    /**
     * @return array|EventList[]
     */
    public function getEventList(): array
    {
        if ($this->getCacheEnabled()) {
            $eventListData = Yii::$app->cache->get($this->getCacheKey());
            if ($eventListData === false) {
                $eventListData = $this->getCompleteEventListWOCache();

                if ($eventListData) {
                    Yii::$app->cache->set(
                        $this->getCacheKey(),
                        $eventListData,
                        0,
                        new TagDependency(['tags' => $this->getCacheTagDependency()])
                    );
                }
            }
        } else {
            $eventListData = $this->getCompleteEventListWOCache();
        }
        return $eventListData;
    }

    /**
     * @param string $key
     * @return array
     */
    public function getEventListByKey(string $key): array
    {

        $data = [];
        $allList = $this->getEventList();
        if ($allList) {
            foreach ($allList as $event) {
                if ($event['el_key'] === $key) {
                    $data[] = $event;
                }
            }
        }
        return $data;
    }

    /**
     * @return string
     */
    final public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    /**
     * @return string
     */
    final public function getCacheTagDependency(): string
    {
        return $this->cacheTagDependency;
    }

    /**
     * @return bool
     */
    final public function getCacheEnabled(): bool
    {
        return $this->cacheEnable;
    }

    /**
     * @return bool
     */
    public function invalidateCache(): bool
    {
        $cacheTagDependency = $this->getCacheTagDependency();
        if ($cacheTagDependency) {
            TagDependency::invalidate(Yii::$app->cache, $cacheTagDependency);
            return true;
        }
        return false;
    }

    /**
     * @param int|null $enableType
     * @param string|null $cronExpression
     * @param $currentTime
     * @return bool
     */
    public static function isDue(?int $enableType, ?string $cronExpression, $currentTime = 'now'): bool
    {
        $response = false;
        $enableType = $enableType ? (int) $enableType : 0;
        if ($enableType === EventList::ET_ENABLED) {
            $response = true;
        } elseif ($enableType === EventList::ET_ENABLED_CONDITION) {
            if (!empty($cronExpression)) {
                if (EventService::isDueCronExpression($cronExpression, $currentTime)) {
                    $response = true;
                }
            }
        } elseif ($enableType === EventList::ET_DISABLED_CONDITION) {
            if (!empty($cronExpression)) {
                if (!EventService::isDueCronExpression($cronExpression, $currentTime)) {
                    $response = true;
                }
            } else {
                $response = true;
            }
        }
        return $response;
    }

    /**
     * @return array
     */
    public function getObjectEventList(): array
    {
        $data = [];
        try {
            $objectList = $this->getObjectList();
            if (!empty($objectList)) {
                foreach ($objectList as $objectKey => $objectName) {
                    if (class_exists($objectKey)) {
                        $object = Yii::createObject($objectKey);
                        foreach ($object->getEventList() as $eventKey => $eventName) {
                            $data[$eventKey] = $eventKey . ' (' . $eventName . ')';
                        }
                    }
                }
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'EventManagerComponent:getObjectEventList:throwable');
        }
        return $data;
    }
}
