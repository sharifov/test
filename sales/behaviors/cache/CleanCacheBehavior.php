<?php

namespace sales\behaviors\cache;

use Yii;
use yii\base\Behavior;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;

/**
 * Class CleanCacheBehavior
 *
 * @property array $tags
 */
class CleanCacheBehavior extends Behavior
{
    public array $tags;

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'cleanCache',
            ActiveRecord::EVENT_AFTER_UPDATE => 'cleanCache',
            ActiveRecord::EVENT_AFTER_DELETE => 'cleanCache',
        ];
    }

    public function cleanCache(): void
    {
        TagDependency::invalidate(Yii::$app->cache, $this->tags);
    }
}