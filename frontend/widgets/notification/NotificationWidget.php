<?php

namespace frontend\widgets\notification;

use common\models\Notifications;
use sales\model\clientChat\entity\ClientChat;
use sales\services\clientChatMessage\ClientChatMessageService;
use yii\bootstrap\Widget;
use yii\caching\TagDependency;

/**
 * Class NotificationWidget
 *
 * @property int $userId
 */
class NotificationWidget extends Widget
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

        $notifications = $this->processPopupNotifications($result['notifications']);

		$clientChatMessageService = \Yii::createObject(ClientChatMessageService::class);
		$totalUnreadMessages = $clientChatMessageService->getCountOfTotalUnreadMessages($this->userId) ?: '';
		$chatsWithUnreadMessages = ClientChat::find()->byIds($clientChatMessageService->getChatWithUnreadMessages($this->userId))->all();

        return $this->render('notifications', [
        	'notifications' => $notifications,
			'count' => $result['count'],
			'totalUnreadMessages' => $totalUnreadMessages,
			'chatsWithUnreadMessages' => $chatsWithUnreadMessages
		]);
    }

    /**
     * @param Notifications[] $notifications
     * @return Notifications[]
     */
    private function processPopupNotifications(array $notifications): array
    {
        $clones = [];
        foreach ($notifications as $notification) {
            $clones[] = clone $notification;
            if ($notification->isMustPopupShow()) {
                $notification->n_popup_show = true;
                $notification->save();
            }
        }
        return $clones;
    }
}
