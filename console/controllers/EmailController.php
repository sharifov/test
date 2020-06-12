<?php

namespace console\controllers;

use common\models\ClientEmail;
use common\models\Email;
use yii\console\Controller;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Console;

class EmailController extends Controller
{
	/**
	 * @param int $limit
	 * @param int $offset
	 */
	public function actionFeelInColumnClientId($limit = 1000, $offset = 0)
	{
		printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
		$time_start = microtime(true);

		$clientEmails = ClientEmail::find()
			->select(['client_id', 'email'])
			->where(new Expression('email is not null'))
			->andWhere(new Expression('trim(email) <> \'\''))
			->limit($limit)
			->offset($offset)
			->asArray()->all();

		$n=0;
		$total = count($clientEmails);
		Console::startProgress(0, $total, 'Counting objects: ', false);
		$connection = \Yii::$app->db;

		foreach ($clientEmails as $clientEmail) {


			$sql = $connection->createCommand()
				->update(
					Email::tableName(),
					['e_client_id' => $clientEmail['client_id']],
					new Expression('(e_email_from = :email or e_email_to = :email) and e_client_id is null'),
					['email' => $clientEmail['email']]
				);

			$sql->execute();

			$n++;
			Console::updateProgress($n, $total);
		}
		Console::endProgress("done." . PHP_EOL);


		$time_end = microtime(true);
		$time = number_format(round($time_end - $time_start, 2), 2);
		printf("\nExecute Time: %s ", $this->ansiFormat($time . ' s', Console::FG_RED));
		printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
	}
}