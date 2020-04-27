<?php

namespace console\controllers;

use common\components\jobs\CreateSaleFromBOJob;
use common\models\CaseSale;
use common\models\Client;
use common\models\ClientEmail;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesSourceType;
use sales\helpers\app\AppHelper;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;

class CaseController extends Controller
{
	/**
	 * @param string $importZipName
	 * @param string $importFileName
	 * @throws Exception
	 */
	public function actionImportRefundData(string $importFileName = 'import_refund.json'): void
	{
		printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
		$time_start = microtime(true);

		$runtimePath = '@console/runtime/';

		if (!file_exists(\Yii::getAlias($runtimePath . $importFileName))) {
			throw new Exception('File: '.$runtimePath . $importFileName.' is not found');
		}

		if (!preg_match('/\.json$/', $importFileName)) {
			throw new Exception('The imported file must be in json format');
		}

		$refundData = file_get_contents(\Yii::getAlias($runtimePath . $importFileName));

		$refundData = json_decode($refundData, true);

		$totalRows = count($refundData);
		$current = 1;
		foreach ($refundData as $refund) {
			$caseSale = CaseSale::findOne(['css_sale_book_id' => $refund['bookingid']]);
			if (!$caseSale) {
				$transaction = Yii::$app->db->beginTransaction();
				try {
					$client = Client::create('ClientName', null, null);
					if (!$client->save(false)) {
						throw new Exception($client->getErrorSummary(true)[0]);
					}
					$clientEmail = ClientEmail::create($refund['email'], $client->getPrimaryKey(), ClientEmail::EMAIL_NOT_SET);
					if (!$clientEmail->save()) {
						throw new Exception($clientEmail->getErrorSummary(true)[0]);
					}
					$case = Cases::createExchangeByImport($client->getPrimaryKey(), $refund['projectid'], $refund['bookingid'], $refund['categoryid'], $refund['subject'], CasesSourceType::OTHER);
					if (!$case->save()) {
						throw new Exception($case->getErrorSummary(true)[0]);
					}

					$job = new CreateSaleFromBOJob();
					$job->case_id = $case->getPrimaryKey();
					$job->order_uid = $refund['bookingid'];
					$job->email = $refund['email'];
					$job->phone = '';
					Yii::$app->queue_job->priority(100)->push($job);

					$transaction->commit();

					echo '----------------' . PHP_EOL;
					printf("\n Processed Data: \n ProjectId - %s \n BookingId - %s \n Email: %s \n CategoryId: %s\n Subject: %s \n",
						$this->ansiFormat($refund['projectid'], Console::FG_GREEN),
						$this->ansiFormat($refund['bookingid'], Console::FG_GREEN),
						$this->ansiFormat($refund['email'], Console::FG_GREEN),
						$this->ansiFormat($refund['categoryid'], Console::FG_GREEN),
						$this->ansiFormat($refund['subject'], Console::FG_GREEN)
					);
					printf("\n Total Rows: %s \n Current row: %s \n Remaining: %s \n",
						$this->ansiFormat($totalRows, Console::FG_GREEN),
						$this->ansiFormat($current, Console::FG_GREEN),
						$this->ansiFormat($totalRows - $current, Console::FG_GREEN));
					echo '----------------' . PHP_EOL;
				} catch (\Throwable $e) {
					$transaction->rollBack();
					throw new Exception($e->getMessage());
				}
			}
			$current++;
		}

		$time_end = microtime(true);
		$time = number_format(round($time_end - $time_start, 2), 2);
		printf("\nExecute Time: %s ", $this->ansiFormat($time . ' s', Console::FG_RED));
		printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
	}
}