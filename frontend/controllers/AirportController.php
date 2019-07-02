<?php

namespace frontend\controllers;

use sales\helpers\airport\AirportFormatHelper;
use sales\services\airport\AirportSearchService;
use yii\filters\AjaxFilter;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\web\Response;

class AirportController extends FController
{
    private $service;

    public function __construct($id, $module, AirportSearchService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors(): array
    {
        $behaviors = [
            [
                'class' => ContentNegotiator::class,
                'only' => ['get-list'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ]
            ],
            [
                'class' => AjaxFilter::class,
                'only' => ['get-list']
            ],
        ];

        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function actionGetList($term = null): array
    {
        $airports = $this->service->search($term);
        $airports = AirportFormatHelper::formatRows($airports, $term);
        return $airports;
    }

}
