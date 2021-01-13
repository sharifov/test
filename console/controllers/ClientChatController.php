<?php

namespace console\controllers;

use common\models\Employee;
use common\models\Notifications;
use common\models\UserOnline;
use common\models\UserProfile;
use frontend\widgets\clientChat\ClientChatAccessMessage;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\ClientChatQuery;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatHold\entity\ClientChatHold;
use sales\model\clientChatLastMessage\entity\ClientChatLastMessage;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\services\clientChatChannel\ClientChatChannelCodeException;
use sales\services\clientChatChannel\ClientChatChannelService;
use sales\services\clientChatService\ClientChatService;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class ClientChatController
 * @package console\controllers
 *
 * @property ClientChatRepository $clientChatRepository
 * @property ClientChatService $clientChatService
 */
class ClientChatController extends Controller
{
    private ClientChatRepository $clientChatRepository;

    private ClientChatService $clientChatService;

    public function __construct(
        $id,
        $module,
        ClientChatRepository $clientChatRepository,
        ClientChatService $clientChatService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->clientChatRepository = $clientChatRepository;
        $this->clientChatService = $clientChatService;
    }

    /**
     * @param int|null $userId
     * @param int $limit
     * @param int $offset
     * @throws \JsonException
     * @throws \yii\base\Exception
     * @throws \yii\httpclient\Exception
     */
    public function actionRcCreateUserProfile(?int $userId = null, int $limit = 5, int $offset = 0)
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));


        $query = Employee::find()->select(['id', 'username', 'nickname', 'email', 'nickname_client_chat'])->leftJoin('user_profile', 'id=up_user_id');
        $query->where(['up_rc_user_id' => null]);
        $query->orWhere(['up_rc_user_id' => '']);

        //echo $query->createCommand()->getRawSql(); exit;

        if ($limit) {
            $query->limit($limit);
        }

        if ($offset) {
            $query->offset($offset);
        }

        if ($userId) {
            $query->andWhere(['id' => $userId]);
        }

        $users = $query->asArray()->all();

        $rocketChat = \Yii::$app->rchat;
        $rocketChat->updateSystemAuth(false);

        foreach ($users as $user) {
            $pass = $rocketChat::generatePassword();

            $rocketChatUsername = $user['nickname_client_chat'] ?: $user['username'];
            $result = $rocketChat->createUser(
                $user['username'],
                $pass,
                $rocketChatUsername,
                $user['email']
            );

            echo "\n-- " . $user['username'] . ' (' . $user['id'] . ') --' . PHP_EOL;

            if (isset($result['error']) && !$result['error']) {
                printf(" - Registered: %s\n", $this->ansiFormat('Username: ' . $result['data']['username'] . ', ID: ' . $result['data']['_id'], Console::FG_BLUE));

                $userProfile = UserProfile::findOne(['up_user_id' => $user['id']]);
                //              if ($userProfile && $userProfile->up_rc_user_id) {
                //                  continue;
                //              }

                if (!$userProfile) {
                    $userProfile = new UserProfile();
                    $userProfile->up_user_id = $user['id'];
                }

                $userProfile->up_rc_user_password = $pass;

                if (empty($result['data']['_id'])) {
                    printf("\n --- Empty result[data][_id]: %s ---\n", $this->ansiFormat(VarDumper::dumpAsString(['user' => $user, 'data' => $result]), Console::FG_RED));
                    continue;
                }

                $userProfile->up_rc_user_id = $result['data']['_id'];

                if (!$userProfile->save()) {
                    $errorMessage = VarDumper::dumpAsString(['profile' => $userProfile->attributes, 'errors' => $userProfile->errors]);
                    \Yii::error($errorMessage, 'Console:ClientChat:RcCreateUserProfile:UserProfile:save');
                    continue;
                }

                $login = $rocketChat->login($user['username'], $pass);

                if (isset($login['error']) && $login['error']) {
                    printf(" - Logined error: %s\n", $this->ansiFormat('Username: ' . $result['data']['username'], Console::FG_RED));
                    $errorMessage = VarDumper::dumpAsString($login['error']);
                    printf(" - Error: %s\n", $this->ansiFormat($errorMessage, Console::FG_RED));
                    continue;
                }

                if (!empty($login['data']['authToken'])) {
                    $userProfile->up_rc_auth_token = $login['data']['authToken'];
                    $userProfile->up_rc_token_expired = $rocketChat::generateTokenExpired();
                    if (!$userProfile->save()) {
                        $errorMessage = VarDumper::dumpAsString(['profile' => $userProfile->attributes, 'errors' => $userProfile->errors]);
                        \Yii::error($errorMessage, 'Console:ClientChat:RcCreateUserProfile:UserProfile:save:login');
                    }
                    printf("\n -- Logined: %s\n", $this->ansiFormat('Username: ' . $result['data']['username'], Console::FG_GREEN));
                }
            } else {
                $errorMessage = $rocketChat::getErrorMessageFromResult($result);
                printf(" - Error2: %s\n", $this->ansiFormat($errorMessage, Console::FG_RED));
            }
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * @param int|null $userId
     * @param int $limit
     * @param int $offset
     * @throws \JsonException
     * @throws \yii\base\Exception
     * @throws \yii\httpclient\Exception
     */
    public function actionRcDeleteUserProfile(?int $userId = null, int $limit = 5, int $offset = 0, int $deleteByUsername = 0)
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));


        $query = Employee::find()->select(['id', 'username', 'nickname', 'email', 'nickname_client_chat'])->leftJoin('user_profile', 'id=up_user_id');
        $query->where(['IS NOT', 'up_rc_user_id', null]);

        //echo $query->createCommand()->getRawSql(); exit;

        if ($limit) {
            $query->limit($limit);
        }

        if ($offset) {
            $query->offset($offset);
        }

        if ($userId) {
            $query->andWhere(['id' => $userId]);
        }

        $users = $query->asArray()->all();

        $rocketChat = \Yii::$app->rchat;
        $rocketChat->updateSystemAuth(false);

        foreach ($users as $user) {
            $userProfile = UserProfile::findOne(['up_user_id' => $user['id']]);

            $result = $rocketChat->deleteUser($userProfile->up_rc_user_id ?? null, $user['username'], $deleteByUsername);

            echo "\n-- " . $user['username'] . ' (' . $user['id'] . ') --' . PHP_EOL;

            if (isset($result['error']) && !$result['error']) {
                printf(" - Deleted: %s\n", $this->ansiFormat('Success', Console::FG_BLUE));


                if (!$userProfile) {
                    continue;
                }

                $userProfile->up_rc_user_password = null;
                $userProfile->up_rc_user_id = null;
                $userProfile->up_rc_auth_token = null;
                $userProfile->up_rc_token_expired = null;


                if (!$userProfile->save()) {
                    $errorMessage = VarDumper::dumpAsString(['profile' => $userProfile->attributes, 'errors' => $userProfile->errors]);
                    \Yii::error($errorMessage, 'Console:ClientChat:RcDeleteUserProfile:UserProfile:save');
                    continue;
                }
            } else {
                $errorMessage = $rocketChat::getErrorMessageFromResult($result);
                printf(" - Error2: %s\n", $this->ansiFormat($errorMessage, Console::FG_RED));
            }
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionDeleteRcCredentialsFromCrm(?int $userId = null, int $limit = 5, int $offset = 0): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $query = UserProfile::find()->where(['not', ['up_rc_user_id' => null]])->orWhere(['<>', 'up_rc_user_id', '']);

        if ($limit) {
            $query->limit($limit);
        }

        if ($offset) {
            $query->offset($offset);
        }

        if ($userId) {
            $query->andWhere(['up_user_id' => $userId]);
        }

        $users = $query->all();

        $rocketChat = \Yii::$app->rchat;
        $rocketChat->updateSystemAuth(false);

        foreach ($users as $user) {
            echo "\n-- UserId " . ' (' . $user->up_user_id . ') --' . PHP_EOL;


            $user->up_rc_user_password = null;
            $user->up_rc_user_id = null;
            $user->up_rc_auth_token = null;
            $user->up_rc_token_expired = null;


            if (!$user->save()) {
                $errorMessage = VarDumper::dumpAsString(['profile' => $user->attributes, 'errors' => $user->errors]);
                \Yii::error($errorMessage, 'Console:ClientChat:actionDeleteRcCredentialsFromCrm:UserProfile:save');
                continue;
            }

            printf(" - Deleted: %s\n", $this->ansiFormat('Success', Console::FG_BLUE));
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionRegisterChannelsInRc(int $channelId = null, string $username = '')
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        if (empty($username)) {
            $username = SettingHelper::getRcNameForRegisterChannelInRc();
        }

        $query = ClientChatChannel::find();
        if ($channelId) {
            $query->byChannel($channelId);
        }
        $channels = $query->all();

        $service = \Yii::createObject(ClientChatChannelService::class);
        foreach ($channels as $channel) {
            try {
                $service->registerChannelInRocketChat($channel->ccc_id, $username);

                printf("\n --- Channel successfully created in rocket chat:  %s ---\n", $this->ansiFormat('ChannelId: ' . $channel->ccc_id, Console::FG_GREEN));
            } catch (\RuntimeException $e) {
                if (ClientChatChannelCodeException::isWarningMessage($e)) {
                    printf("\n --- %s ---\n", $this->ansiFormat($e->getMessage(), Console::FG_YELLOW));
                } else {
                    printf("\n --- Error has occurred:  %s ---\n", $this->ansiFormat($e->getMessage(), Console::FG_RED));
                }
            } catch (\Throwable $e) {
                printf("\n --- Critical Error has occurred:  %s ---\n", $this->ansiFormat(AppHelper::throwableFormatter($e), Console::FG_RED));
            }
        }
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionIdle(): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $onlineChannels = [];
        $onlineProcessed = 0;
        $onlineFailed = 0;
        $offlineChannels = [];
        $offlineProcessed = 0;
        $offlineFailed = 0;

        $timeStart = microtime(true);

        $onlineMinutes = (int)(\Yii::$app->params['settings']['client_chat_idle_timeout_online_user'] ?? 0);
        $offlineMinutes = (int)(\Yii::$app->params['settings']['client_chat_idle_timeout_offline_user'] ?? 0);

        if ($onlineMinutes === 0 && $offlineMinutes === 0) {
            echo Console::renderColoredString('%w --- Script stopped. Setting "client_chat_idle_timeout_online_user" and "client_chat_idle_timeout_offline_user" eq "0" %n'), PHP_EOL;
        }

        if ($onlineMinutes) {
            $onlineDtOlder = (new \DateTime('now'))->modify('-' . $onlineMinutes . ' minutes')->format('Y-m-d H:i:s');
            $inactiveChatsWithOnlineUsers = ClientChat::find()
                ->innerJoinWith('lastMessage', false)
                ->leftJoin(UserOnline::tableName(), 'uo_user_id = cch_owner_user_id')
                ->byStatus(ClientChat::STATUS_IN_PROGRESS)
                ->andWhere(['<=', 'cclm_dt', $onlineDtOlder])
                ->andWhere(['cclm_type_id' => ClientChatLastMessage::TYPE_CLIENT])
                ->andWhere(['IS NOT', 'cch_owner_user_id', null])
                ->andWhere(['IS NOT', 'uo_user_id', null])
                ->orderBy(['cch_id' => SORT_ASC])
                ->all();

            [$onlineChannels, $onlineProcessed, $onlineFailed] = $this->transferChatToIdle($inactiveChatsWithOnlineUsers);
        }

        if ($offlineMinutes) {
            $offlineDtOlder = (new \DateTime('now'))->modify('-' . $offlineMinutes . ' minutes')->format('Y-m-d H:i:s');
            $inactiveChatsWithOfflineUsers = ClientChat::find()
                ->innerJoinWith('lastMessage', false)
                ->leftJoin(UserOnline::tableName(), 'uo_user_id = cch_owner_user_id')
                ->byStatus(ClientChat::STATUS_IN_PROGRESS)
                ->andWhere(['<=', 'cclm_dt', $offlineDtOlder])
                ->andWhere(['cclm_type_id' => ClientChatLastMessage::TYPE_CLIENT])
                ->andWhere(['IS NOT', 'cch_owner_user_id', null])
                ->andWhere(['IS', 'uo_user_id', null])
                ->orderBy(['cch_id' => SORT_ASC])
                ->all();

            [$offlineChannels, $offlineProcessed, $offlineFailed] = $this->transferChatToIdle($inactiveChatsWithOfflineUsers);
        }

        $channels = array_unique(array_merge($onlineChannels, $offlineChannels));

        if ($channels) {
            foreach ($channels as $channelId) {
                Notifications::pub(
                    [ClientChatChannel::getPubSubKey($channelId)],
                    'reloadClientChatList'
                );
            }
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g') . PHP_EOL;
        echo Console::renderColoredString('%g --- Processed(Online): %w[' . $onlineProcessed . '] %g Failed(Online): %w[' . $onlineFailed . '] %n %g') . PHP_EOL;
        echo Console::renderColoredString('%g --- Processed(Offline): %w[' . $offlineProcessed . '] %g Failed(Offline): %w[' . $offlineFailed . '] %n %g') . PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

//        Yii::info(VarDumper::dumpAsString([
//            'Processed' => $processed,
//            'Failed' => $failed,
//            'Minutes' => $minutes,
//            'Execute Time' => $time . ' sec',
//            'End Time' => date('Y-m-d H:i:s'),
//        ]), 'info\ClientChatController:actionIdle:result');
    }

    /**
     * @param ClientChat[] $inactiveChats
     */
    private function transferChatToIdle(array $inactiveChats): array
    {
        $channels = [];
        $processed = 0;
        $failed = 0;
        foreach ($inactiveChats as $clientChat) {
            try {
                $clientChat->idle(null, ClientChatStatusLog::ACTION_AUTO_IDLE);
                $this->clientChatRepository->save($clientChat);
                $this->clientChatService->sendRequestToUsers($clientChat);
                $channels[] = $clientChat->cch_channel_id;
                $processed++;
            } catch (\Throwable $throwable) {
                Yii::error(
                    AppHelper::throwableFormatter($throwable),
                    'ClientChatController:actionIdle:throwable'
                );
                echo Console::renderColoredString('%r --- Error : ' . $throwable->getMessage() . ' %n'), PHP_EOL;
                $failed++;
            }
        }
        return [$channels, $processed, $failed];
    }

    public function actionHoldToProgress(): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $channels = [];
        $processed = $failed = 0;
        $timeStart = microtime(true);
        $enable = \Yii::$app->params['settings']['client_chat_job_hold_to_in_progress_enable'] ?? false;

        if (!$enable) {
            echo Console::renderColoredString('%w --- Script stopped. Setting "client_chat_job_hold_to_in_progress_enable" eq "FALSE" %n'), PHP_EOL;
            return;
        }

        $holdDeadlineChats = ClientChat::find()
            ->innerJoinWith('clientChatHold', false)
            ->byStatus(ClientChat::STATUS_HOLD)
            ->andWhere(['<=', 'cchd_deadline_dt', (new \DateTime('now'))->format('Y-m-d H:i:s')])
            ->orderBy(['cch_id' => SORT_ASC])
            ->indexBy('cch_id')
            ->all();

        foreach ($holdDeadlineChats as $clientChat) {
            try {
                /** @var ClientChat $clientChat */
                $clientChat->inProgress(null, ClientChatStatusLog::ACTION_AUTO_REVERT_TO_PROGRESS);
                $this->clientChatRepository->save($clientChat);

                if ($holdRow = ClientChatHold::findOne(['cchd_cch_id' => $clientChat->cch_id])) {
                    $holdRow->delete();
                }
                Notifications::pub(
                    [ClientChatChannel::getPubSubKey($clientChat->cch_channel_id)],
                    'reloadChatInfo',
                    ['data' => ClientChatAccessMessage::chatInProgress($clientChat->cch_id)]
                );
                $channels[$clientChat->cch_channel_id] = $clientChat->cch_channel_id;
                $processed++;
            } catch (\Throwable $throwable) {
                Yii::error(
                    AppHelper::throwableFormatter($throwable),
                    'ClientChatController:actionHoldToProgress:throwable'
                );
                echo Console::renderColoredString('%r --- Error : ' . $throwable->getMessage() . ' %n'), PHP_EOL;
                $failed++;
            }
        }

        if ($channels) {
            foreach ($channels as $channelId) {
                Notifications::pub(
                    [ClientChatChannel::getPubSubKey($channelId)],
                    'reloadClientChatList'
                );
            }
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %g Processed: %w[' . $processed . '] %g Failed: %w[' . $failed . '] %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

//        Yii::info(VarDumper::dumpAsString([
//            'Processed' => $processed,
//            'Failed' => $failed,
//            'Execute Time' => $time . ' sec',
//            'End Time' => date('Y-m-d H:i:s'),
//        ]), 'info\ClientChatController:actionHoldToProgress:result');
    }

    public function actionCloseToArchiveOnTimeout(int $hourTimeout = 0): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

        $processed = $failed = 0;
        $timeStart = microtime(true);

        $closedChats = ClientChatQuery::getLastUpdatedClosedChatsIdsByTimeout($hourTimeout ?: SettingHelper::getClientChatSoftCloseTimeoutHours());

        foreach ($closedChats as $clientChat) {
            try {
                if (ClientChatQuery::isExistsNotClosedArchivedChatByRid($clientChat->cch_rid)) {
                    continue;
                }

                $clientChat->archive(null, ClientChatStatusLog::ACTION_TIMEOUT_FINISH);
                $this->clientChatRepository->save($clientChat);

                $processed++;

                Notifications::pub(
                    ['chat-' . $clientChat->cch_id],
                    'refreshChatPage',
                    ['data' => ClientChatAccessMessage::chatArchive($clientChat->cch_id)]
                );
            } catch (\Throwable $e) {
                Yii::error(
                    AppHelper::throwableFormatter($e),
                    'ClientChatController:actionCloseToArchiveOnTimeout:throwable'
                );
                echo Console::renderColoredString('%r --- Error : ' . $e->getMessage() . ' %n'), PHP_EOL;
                $failed++;
            }
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);

        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time .
            ' s] %g Processed: %w[' . $processed . '] %g Failed: %w[' . $failed . '] %n'), PHP_EOL;

        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;

//        Yii::info(VarDumper::dumpAsString([
//            'Processed' => $processed,
//            'Failed' => $failed,
//            'Execute Time' => $time . ' sec',
//            'End Time' => date('Y-m-d H:i:s'),
//        ]), 'info\ClientChatController:actionCloseToArchiveOnTimeout:result');
    }
}
