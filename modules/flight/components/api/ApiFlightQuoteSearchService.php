<?php

namespace modules\flight\components\api;

use yii\helpers\VarDumper;

class ApiFlightQuoteSearchService extends ApiService
{
	/**
	 * @param array $data
	 * @return array
	 */
	public function search(array $data): array
	{
		$out = ['error' => false, 'data' => []];

		try {
			$response = $this->sendRequest(\Yii::$app->params['searchApiUrl'], $data, 'GET');

			if ($response->isOk) {
				$out['data'] = $response->data;
			} else {
				$out['error'] = 'Error ('.$response->statusCode.'): ' . $response->content;
				\Yii::error(VarDumper::dumpAsString($out['error'], 10), 'Flight::Component::ApiFlightQuoteSearchService::search');
			}
		} catch (\Throwable $e) {
			\Yii::error(VarDumper::dumpAsString($e, 10), 'Flight::Component::ApiFlightQuoteSearchService::search::throwable');
			$out['error'] = 'ApiHotelService error: ' . $e->getMessage();
		}

		return $out;
	}
}