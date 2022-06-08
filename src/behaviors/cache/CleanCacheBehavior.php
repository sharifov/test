<?php

namespace src\behaviors\cache;

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
            ActiveRecord::EVENT_BEFORE_INSERT => 'cleanCache',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'cleanCache',
            ActiveRecord::EVENT_BEFORE_DELETE => 'cleanCache',
        ];
    }

    public function cleanCache(): void
    {
        TagDependency::invalidate(Yii::$app->cache, $this->tags);
    }
}
