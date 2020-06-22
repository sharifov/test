<?php


namespace console\controllers;


use common\models\Employee;
use yii\console\Controller;

class ClientChatController extends Controller
{
	public function actionRcCreateUserProfile(?int $userId = null, int $limit = 5)
	{
		$query = Employee::find()->select(['username', 'full_name', 'email']);

		if ($limit) {
			$query->limit($limit);
		}

		if ($userId) {
			$query->andWhere(['id' => $userId]);
		}

		$users = $query->asArray()->all();

		foreach ($users as $user) {
			
		}
	}
}