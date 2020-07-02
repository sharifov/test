<?php


namespace frontend\widgets\clientChat;


use sales\helpers\setting\SettingHelper;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\base\Widget;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatAccessWidget
 * @package frontend\widgets\clientChat
 *
 * @property int $userId
 */
class ClientChatAccessWidget extends Widget
{
	/**
	 * @var int $userId
	 */
	public int $userId;

	/**
	 * @return string|null
	 */
	public function run(): ?string
	{
		$_self = $this;

		if (!SettingHelper::isClientChatEnabled()) {
			return false;
		}
//		$result = ClientChatCache::getCache()->getOrSet(ClientChatCache::getKey($this->userId), static function () use ($_self) {
//			return [
//				'access' => ClientChatUserAccess::pendingRequests($_self->userId),
//			];
//		}, null, new TagDependency(['tags' => ClientChatCache::getTags($this->userId)]));

		$isPjax = \Yii::$app->request->isPjax;

		$result['access'] = ClientChatUserAccess::pendingRequests($_self->userId);

		return $this->render('cc_request', ['access' => $result['access'], 'isPjax' => $isPjax]);
	}
}