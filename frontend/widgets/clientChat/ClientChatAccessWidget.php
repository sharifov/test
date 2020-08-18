<?php


namespace frontend\widgets\clientChat;


use common\components\i18n\Formatter;
use common\models\Employee;
use sales\auth\Auth;
use sales\helpers\setting\SettingHelper;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatAccessWidget
 * @package frontend\widgets\clientChat
 *
 * @property int $userId
 * @property bool $open
 */
class ClientChatAccessWidget extends Widget
{
	/**
	 * @var self $instance
	 */
	private static $instance;

	/**
	 * @var int $userId
	 */
	public int $userId;

	/**
	 * @var bool $open
	 */
	public bool $open = false;

	public static function getInstance(): ClientChatAccessWidget
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @return string|null
	 */
	public function run(): ?string
	{
		$_self = $this;

		$user = \Yii::$app->user->identity;
		if (!SettingHelper::isClientChatEnabled() || ($user instanceof Employee && !Auth::can('/client-chat/index'))) {
			return false;
		}
//		$result = ClientChatCache::getCache()->getOrSet(ClientChatCache::getKey($this->userId), static function () use ($_self) {
//			return [
//				'access' => ClientChatUserAccess::pendingRequests($_self->userId),
//			];
//		}, null, new TagDependency(['tags' => ClientChatCache::getTags($this->userId)]));

		$result['access'] = ClientChatUserAccess::pendingRequests($_self->userId);

		$user = Employee::findOne(['id' => $this->userId]);

		if ($user) {
			$formatter = new Formatter();
			$formatter->timeZone = $user->timezone;
		} else {
			$formatter = \Yii::$app->formatter;
		}

		return $this->render('cc_request', ['access' => $result['access'], 'open' => $this->open, 'formatter' => $formatter]);
	}
}