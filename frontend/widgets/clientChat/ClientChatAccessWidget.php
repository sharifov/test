<?php

namespace frontend\widgets\clientChat;

use common\components\i18n\Formatter;
use common\models\Employee;
use sales\auth\Auth;
use sales\helpers\setting\SettingHelper;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserAccess\entity\search\ClientChatUserAccessSearch;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChatAccessWidget
 * @package frontend\widgets\clientChat
 *
 * @property int $userId
 * @property int|null $userAccessId
 * @property bool $open
 * @property int $page
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
     * @var int|null $userAccessId
     */
    public ?int $userAccessId = null;

    /**
     * @var int
     */
    public int $page = 0;

    /**
     * @var int
     */
    public int $countDisplayedRequests = 0;

    /**
     * @var int
     */
    private int $limit = 20;

    /**
     * @var bool $open
     */
    public bool $open = false;

    public static function getInstance(): ClientChatAccessWidget
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        self::$instance->limit = SettingHelper::getChatWidgetLimitRequests();
        return self::$instance;
    }

    /**
     * @return string|null
     */
    public function run(): ?string
    {
        $user = Employee::findOne(['id' => $this->userId]);

        if (!SettingHelper::isClientChatEnabled() || !$user || !$user->can('/client-chat/index')) {
            return false;
        }
        //      $result = ClientChatCache::getCache()->getOrSet(ClientChatCache::getKey($this->userId), static function () use ($_self) {
        //          return [
        //              'access' => ClientChatUserAccess::pendingRequests($_self->userId),
        //          ];
        //      }, null, new TagDependency(['tags' => ClientChatCache::getTags($this->userId)]));

        if ($user) {
            $formatter = new Formatter();
            $formatter->timeZone = $user->timezone;
        } else {
            $formatter = \Yii::$app->formatter;
        }

        //      $search = new ClientChatUserAccessSearch();
        //      $result = $search->searchPendingRequests($this->userId, $this->page, $this->limit * ($this->page+1));
        return $this->render('cc_request', ['access' => [], 'open' => $this->open, 'formatter' => $formatter, 'page' => $this->page]);
    }

    public function fetchItems(): array
    {
        $user = Employee::findOne(['id' => $this->userId]);
        if ($user) {
            $formatter = new Formatter();
            $formatter->timeZone = $user->timezone;
        } else {
            $formatter = \Yii::$app->formatter;
        }
        $search = new ClientChatUserAccessSearch();
        $accessItems = $search->searchPendingRequests($this->userId, $this->getOffset(), $this->limit);
        foreach ($accessItems as $key => $access) {
            $accessItems[$key]['html'] = $this->render('cc_request_item', ['access' => $access, 'formatter' => $formatter, 'user' => $user]);
            $accessItems[$key]['ccua_created_t'] = strtotime($access['ccua_created_dt']);
            $accessItems[$key]['cch_created_t'] = strtotime($access['cch_created_dt']);
        }
        return $accessItems;
    }

    public function getTotalItems()
    {
        $search = new ClientChatUserAccessSearch();
        return $search->getTotalItems($this->userId);
    }

    public function fetchOneItem(): array
    {
        $user = Employee::findOne(['id' => $this->userId]);
        if ($user) {
            $formatter = new Formatter();
            $formatter->timeZone = $user->timezone;
        } else {
            $formatter = \Yii::$app->formatter;
        }

        $search = new ClientChatUserAccessSearch();
        $accessItem = $search->getPendingRequestByChatUserAccessId($this->userAccessId);

        $accessItem['html'] = $this->render('cc_request_item', ['access' => $accessItem, 'formatter' => $formatter, 'user' => $user]);
        $accessItem['ccua_created_t'] = strtotime($accessItem['ccua_created_dt']);
        $accessItem['cch_created_t'] = strtotime($accessItem['cch_created_dt']);

        return $accessItem;
    }

    private function getOffset()
    {
        //      $offset = $this->page * $this->limit;
        return $this->countDisplayedRequests;
    }
}
