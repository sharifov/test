<?php

namespace modules\flight\controllers;

use frontend\controllers\FController;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\flight\src\repositories\flight\FlightRepository;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class FlightTestController extends FController
{
	public $enableCsrfValidation = false;

	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		$behaviors = [
			'access' => [
				'class' => AccessControl::class,
				'only' => ['login', 'logout', 'signup'],
				'rules' => [
					[
						'allow' => true,
						'actions' => ['TestAddQuote', 'testAddQuote'],
						'roles' => ['?'],
					],
				],
			]
		];

		return ArrayHelper::merge(parent::behaviors(), $behaviors);
	}

    public function actionTestAddQuote()
	{
		$data = @json_decode(\Yii::$app->request->rawBody, true);

		$flightQuoteManageService = \Yii::createObject(FlightQuoteManageService::class);
		$flightRepository = \Yii::createObject(FlightRepository::class);

		$userId = 464;
		$flightId = 31;

		$flight = $flightRepository->find($flightId);

		try {
			$flightQuoteManageService->create($flight, $data, $userId);
		} catch (\Throwable $e) {
			echo $e->getMessage() . '; In File: ' . $e->getFile() . '; On Line: ' . $e->getLine();
			die;
		}

		die('success');
	}

}
