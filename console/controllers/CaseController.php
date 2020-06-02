<?php

namespace console\controllers;

use common\components\jobs\CreateSaleFromBOJob;
use common\models\CaseSale;
use common\models\Client;
use common\models\ClientEmail;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesSourceType;
use sales\entities\cases\CasesStatus;
use sales\helpers\app\AppHelper;
use sales\model\saleTicket\useCase\create\SaleTicketService;
use sales\repositories\cases\CasesRepository;
use sales\repositories\cases\CasesSaleRepository;
use sales\services\cases\CasesSaleService;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\db\Query;
use yii\helpers\Console;
use yii\web\BadRequestHttpException;

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

	public function actionRefreshCaseSales(int $caseId = null, int $saleId = null, int $limit = 1000, int $offset = 0)
	{
		printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
		$time_start = microtime(true);

		try {

			$transaction = Yii::$app->db->beginTransaction();

			$query = new Query();
			$query->addSelect(['cs_id', 'css_sale_id'])->from('cases')->where(['cs_status' => CasesStatus::STATUS_PROCESSING]);

			if ($saleId) {
				$query->innerJoin('case_sale', 'case_sale.css_cs_id = cases.cs_id and case_sale.css_sale_id = :saleId', ['saleId' => $saleId]);
			} else {
				$query->innerJoin('case_sale', 'case_sale.css_cs_id = cases.cs_id');
			}

			if ($caseId) {
				$query->andWhere(['cs_id' => $caseId]);
			}
			$query->limit($limit)->offset($offset);

			$result = $query->all();

			$caseSaleService = Yii::createObject(CasesSaleService::class);
			$saleTicketService = Yii::createObject(SaleTicketService::class);

			$n=0;
			$total = count($result);
			Console::startProgress(0, $total, 'Counting objects: ', false);

			$boErrors = [];

			$caseSaleRepository = Yii::createObject(CasesSaleRepository::class);

			foreach ($result as $item) {
				try {
					$saleData = $caseSaleService->detailRequestToBackOffice((int)$item['css_sale_id'], 1, 120, 1);
				} catch (BadRequestHttpException $e) {
					$n++;
					$boErrors[] = "Loop: {$n}; BO error occurred: caseId: {$item['cs_id']}; saleId: {$item['css_sale_id']}";
					Console::updateProgress($n, $total);
					continue;
				}
				$caseSale = $caseSaleRepository->getSaleByPrimaryKeys((int)$item['cs_id'], (int)$item['css_sale_id']);
//				$caseSale = $caseSaleService->refreshOriginalSaleData($caseSale, $case, $saleData);
				$saleTicketService->refreshSaleTicketBySaleData((int)$item['cs_id'], $caseSale, $saleData);

				$n++;
				Console::updateProgress($n, $total);
			}

			Console::endProgress("done." . PHP_EOL);

			if (!empty($boErrors)) {
				printf("\nBo errors occurred: %s ", $this->ansiFormat(implode('; ' . PHP_EOL, $boErrors), Console::FG_RED));
			}


			$transaction->commit();
		} catch (\Throwable $e) {
			$transaction->rollBack();
			printf("\nError occurred: %s ", $this->ansiFormat($e->getMessage() . '; File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), Console::FG_RED));
		}

		$time_end = microtime(true);
		$time = number_format(round($time_end - $time_start, 2), 2);
		printf("\nExecute Time: %s ", $this->ansiFormat($time . ' s', Console::FG_RED));
		printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
	}
}