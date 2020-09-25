<?php

namespace console\controllers;

use common\models\Employee;
use common\models\UserProfile;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\services\clientChatChannel\ClientChatChannelCodeException;
use sales\services\clientChatChannel\ClientChatChannelService;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class ClientChatController
 * @package console\controllers
 */
class ClientChatController extends Controller
{
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

            echo "\n-- " . $user['username'] . ' ('.$user['id'].') --' . PHP_EOL;

			if (isset($result['error']) && !$result['error']) {


                printf(" - Registered: %s\n", $this->ansiFormat('Username: ' . $result['data']['username'] . ', ID: ' . $result['data']['_id'], Console::FG_BLUE));

				$userProfile = UserProfile::findOne(['up_user_id' => $user['id']]);
//				if ($userProfile && $userProfile->up_rc_user_id) {
//					continue;
//				}

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

				if(!$userProfile->save()) {
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
                    if(!$userProfile->save()) {
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

            echo "\n-- " . $user['username'] . ' ('.$user['id'].') --' . PHP_EOL;

            if (isset($result['error']) && !$result['error']) {


                printf(" - Deleted: %s\n", $this->ansiFormat('Success', Console::FG_BLUE));


                if (!$userProfile) {
                    continue;
                }

                $userProfile->up_rc_user_password = null;
                $userProfile->up_rc_user_id = null;
                $userProfile->up_rc_auth_token = null;
                $userProfile->up_rc_token_expired = null;


                if(!$userProfile->save()) {
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

			echo "\n-- UserId " . ' ('.$user->up_user_id.') --' . PHP_EOL;


			$user->up_rc_user_password = null;
			$user->up_rc_user_id = null;
			$user->up_rc_auth_token = null;
			$user->up_rc_token_expired = null;


			if(!$user->save()) {
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
}