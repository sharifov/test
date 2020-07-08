<?php

namespace frontend\widgets\notification;

use common\models\Notifications;
use sales\model\clientChat\entity\ClientChat;
use sales\services\clientChatMessage\ClientChatMessageService;
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

//        $clientChatNotifResult = NotificationCache::getCache()->getOrSet(NotificationCache::getClientChatKey($this->userId), function () {
//        	$clientChatMessageService = \Yii::createObject(ClientChatMessageService::class);
//            return [
//				'totalUnreadMessages' => $clientChatMessageService->getCountOfTotalUnreadMessages($this->userId) ?: '',
//				'chatsWithUnreadMessages' => ClientChat::find()->byIds($clientChatMessageService->getChatWithUnreadMessages($this->userId))->all()
//            ];
//        }, null, new TagDependency(['tags' => NotificationCache::getClientChatTags($this->userId)]));

		$clientChatMessageService = \Yii::createObject(ClientChatMessageService::class);
		$totalUnreadMessages = $clientChatMessageService->getCountOfTotalUnreadMessages($this->userId) ?: '';
		$chatsWithUnreadMessages = ClientChat::find()->byIds($clientChatMessageService->getChatWithUnreadMessages($this->userId))->all();

		return $this->render('notifications-socket', [
        	'notifications' => $result['notifications'],
			'count' => $result['count'],
			'totalUnreadMessages' => $totalUnreadMessages,
			'chatsWithUnreadMessages' => $chatsWithUnreadMessages
		]);
    }
}
