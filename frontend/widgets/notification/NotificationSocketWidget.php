<?php

namespace frontend\widgets\notification;

use common\models\Notifications;
use yii\bootstrap\Widget;
use yii\caching\TagDependency;

/**
 * Class NotificationSocketWidget
 *
 * @property int $userId
 */
class NotificationSocketWidget extends Widget
{
    public $userId;

    public function run(): string
    {
        $result = NotificationCache::getCache()->getOrSet(NotificationCache::getKey($this->userId), function () {
            return [
                'count' => Notifications::findNewCount($this->userId),
                'notifications' => Notifications::findNew($this->userId),
            ];
        }, null, new TagDependency(['tags' => NotificationCache::getTags($this->userId)]));

        return $this->render('notifications-socket', ['notifications' => $result['notifications'], 'count' => $result['count']]);
    }
}
