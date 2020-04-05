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

class CaseController extends Controller
{
	/**
	 * @param string $importZipName
	 * @param string $importFileName
	 * @throws Exception
	 */
	public function actionImportRefundData(string $importZipName = 'import_refund.zip', string $importFileName = 'import_refund.json'): void
	{
		$runtimePath = '@console/runtime/';

		if (!preg_match('/\.zip$/', $importZipName)) {
			throw new Exception('Imported archive must be compressed to zip format');
		}

		if (!file_exists(\Yii::getAlias($runtimePath . $importZipName))) {
			throw new Exception('File: '.$runtimePath . $importZipName.' is not found');
		}

		$zip = new \ZipArchive();
		if (!$zip->open(\Yii::getAlias($runtimePath . $importZipName))) {
			throw new Exception('Unzip false');
		}

		$exportedFile = false;
		for ($i = 0; $i < $zip->numFiles; $i++) {
			$extractedFileName = $zip->getNameIndex($i);
			if ($extractedFileName === $importFileName) {
				$zip->extractTo(\Yii::getAlias('@console/runtime/'));
				$exportedFile = true;
				break;
			}
		}
		$zip->close();

		if (!$exportedFile) {
			throw new Exception('File: ' . $importFileName . ' was not extracted from ' . $importZipName);
		}

		$refundData = file_get_contents(\Yii::getAlias('@console/runtime/' . $importFileName));

		$refundData = json_decode($refundData, true);

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
					$case = Cases::createExchangeByImport($client->getPrimaryKey(), $refund['projectid'], 25, 'Corona Virus form', CasesSourceType::OTHER);
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
				} catch (\Throwable $e) {
					$transaction->rollBack();
					throw new Exception($e->getMessage());
				}
			}
		}
	}
}