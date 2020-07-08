<?php

namespace console\controllers;

use common\models\Employee;
use common\models\UserProfile;
use yii\console\Controller;
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


		$query = Employee::find()->select(['id', 'username', 'full_name', 'email'])->leftJoin('user_profile', 'id=up_user_id');
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

			$pass = \Yii::$app->security->generateRandomString(20);

			$result = $rocketChat->createUser(
				$user['username'],
				$pass,
				$user['full_name'] ?: $user['username'],
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
                    $userProfile->up_rc_token_expired = date('Y-m-d H:i:s', strtotime("+60 days"));
                    if(!$userProfile->save()) {
                        $errorMessage = VarDumper::dumpAsString(['profile' => $userProfile->attributes, 'errors' => $userProfile->errors]);
                        \Yii::error($errorMessage, 'Console:ClientChat:RcCreateUserProfile:UserProfile:save:login');

                    }
                    printf("\n -- Logined: %s\n", $this->ansiFormat('Username: ' . $result['data']['username'], Console::FG_GREEN));
                }

			} else {
			    if (isset($result['error'])) {

			        $errorArr = @json_decode($result['error'], true, 512, JSON_THROW_ON_ERROR);

			        if (isset($errorArr['message'])) {
                        $errorMessage = $errorArr['message'];
                    } elseif (isset($errorArr['error'])) {
                        $errorMessage = $errorArr['error'];
                    } else {
                        $errorMessage = VarDumper::dumpAsString($result['error']);
                    }
                } else {
                    $errorMessage = VarDumper::dumpAsString($result);
                }
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
    public function actionRcDeleteUserProfile(?int $userId = null, int $limit = 5, int $offset = 0)
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));


        $query = Employee::find()->select(['id', 'username', 'full_name', 'email'])->leftJoin('user_profile', 'id=up_user_id');
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

            $result = $rocketChat->deleteUser($user['username']);

            echo "\n-- " . $user['username'] . ' ('.$user['id'].') --' . PHP_EOL;

            if (isset($result['error']) && !$result['error']) {


                printf(" - Deleted: %s\n", $this->ansiFormat('Success', Console::FG_BLUE));

                $userProfile = UserProfile::findOne(['up_user_id' => $user['id']]);

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
                if (isset($result['error'])) {

                    $errorArr = @json_decode($result['error'], true, 512, JSON_THROW_ON_ERROR);

                    if (isset($errorArr['message'])) {
                        $errorMessage = $errorArr['message'];
                    } elseif (isset($errorArr['error'])) {
                        $errorMessage = $errorArr['error'];
                    } else {
                        $errorMessage = VarDumper::dumpAsString($result['error']);
                    }
                } else {
                    $errorMessage = VarDumper::dumpAsString($result);
                }
                printf(" - Error2: %s\n", $this->ansiFormat($errorMessage, Console::FG_RED));
            }
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
}