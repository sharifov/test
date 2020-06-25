<?php

namespace console\controllers;

use common\components\RocketChat;
use common\models\Employee;
use common\models\UserProfile;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class ClientChatController extends Controller
{
	public function actionRcCreateUserProfile(?int $userId = null, int $limit = 5)
	{
		printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));


		$query = Employee::find()->select(['id', 'username', 'full_name', 'email']);

		if ($limit) {
			$query->limit($limit);
		}

		if ($userId) {
			$query->andWhere(['id' => $userId]);
		}

		$users = $query->asArray()->all();

		/** @var RocketChat $rocketChat */
		$rocketChat = \Yii::$app->rchat;
		foreach ($users as $user) {
			$pass = \Yii::$app->security->generateRandomString(20);
			$result = $rocketChat->createUser(
				$user['username'],
				$pass,
				$user['full_name'],
				$user['email']
			);

			if (!empty($result['error'])) {
				$userProfile = UserProfile::findOne(['up_user_id' => $user['id']]);
				if ($userProfile && $userProfile->up_rc_user_id) {
					continue;
				}

				if (!$userProfile) {
					$userProfile = new UserProfile();
					$userProfile->up_user_id = $user['id'];
				}

				$userProfile->up_rc_user_password = $pass;
				$userProfile->up_rc_user_id = $result['data']['_id'];
				$userProfile->save();

				$login = $rocketChat->login($user['username'], $pass);

				if (!$login['error']) {
					$userProfile->up_rc_auth_token = $login['data']['authToken'];
					$userProfile->up_rc_token_expired = date('Y-m-d H:i:s', strtotime("+60 days"));
					$userProfile->save();

					continue;
				}

				printf("\n --- RC login error occurred: %s ---\n", $this->ansiFormat(VarDumper::dumpAsString($result), Console::FG_RED));
			} else {
				printf("\n --- RC create user error occurred: %s ---\n", $this->ansiFormat(VarDumper::dumpAsString($result), Console::FG_RED);
			}
		}

		printf("\n --- Rocket chat user profiles has been created %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_GREEN));
	}
}