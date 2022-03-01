<?php

namespace modules\eventManager\components;

use modules\eventManager\src\entities\EventList;
use Yii;
use yii\base\Component;
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
        $eventListContent = $this->getEventListContent();
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
    final public function getEventListContentWOCache(): array
    {

        $rows = EventList::find()->select([
            'el_key',
            'el_enable_type',
            'el_enable_log',
            'el_break',
            'el_sort_order',
            'el_cron_expression',
            'el_condition'
        ])
//            ->where(['ap_enabled' => true])
            ->orderBy(['el_sort_order' => SORT_ASC, 'el_id' => SORT_ASC])->asArray()->all();

        return $rows;
    }

    /**
     * @return array|EventList[]
     */
    public function getEventListContent(): array
    {
        if ($this->getCacheEnabled()) {
            $eventListContent = Yii::$app->cache->get($this->getCacheKey());
            if ($eventListContent === false) {
                $eventListContent = $this->getEventListContentWOCache();

                if ($eventListContent) {
                    Yii::$app->cache->set(
                        $this->getCacheKey(),
                        $eventListContent,
                        0,
                        new TagDependency(['tags' => $this->getCacheTagDependency()])
                    );
                }
            }
        } else {
            $eventListContent = $this->getEventListContentWOCache();
        }
        return $eventListContent;
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
}
