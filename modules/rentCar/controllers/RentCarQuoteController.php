<?php

namespace modules\rentCar\controllers;

use frontend\controllers\FController;
use modules\rentCar\components\ApiRentCarService;
use modules\rentCar\RentCarModule;
use modules\rentCar\src\entity\dto\RentCarProductQuoteDto;
use modules\rentCar\src\entity\dto\RentCarQuoteDto;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\helpers\RentCarDataParser;
use modules\rentCar\src\helpers\RentCarQuoteHelper;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class RentCarQuoteController
 */
class RentCarQuoteController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    public function actionSearchAjax()
    {
        $rentCarId = (int) Yii::$app->request->get('id');
        $rentCar = $this->findRentCar($rentCarId);

        $apiRentCarService = RentCarModule::getInstance()->apiService;

        $result = $apiRentCarService->search(
            $rentCar->prc_pick_up_code,
            $rentCar->prc_pick_up_date,
            $rentCar->prc_pick_up_time,
            $rentCar->prc_drop_off_time,
            $rentCar->prc_drop_off_code,
            $rentCar->prc_drop_off_date
        );

        $dataList = \Yii::$app->cacheFile->get($rentCar->prc_request_hash_key);

        if ($dataList === false) {
            if ($dataList = RentCarDataParser::prepareDataList($result['data'])) {
                \Yii::$app->cacheFile->set($rentCar->prc_request_hash_key, $dataList, 300);
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $dataList,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->renderAjax('search/_search_quotes', [
            'dataProvider' => $dataProvider,
            'rentCar' => $rentCar,
        ]);
    }

    public function actionAddQuote(): array
    {
        $rentCarId = (int) Yii::$app->request->get('id');
        $token = Yii::$app->request->post('token');

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            if (!$rentCarId) {
                throw new \RuntimeException('RentCarId param not found', 2);
            }
            if (!$token) {
                throw new \RuntimeException('Token param not found', 3);
            }
            $rentCar = $this->findRentCar($rentCarId);

            if (!$dataList = \Yii::$app->cacheFile->get($rentCar->prc_request_hash_key)) {
                throw new \RuntimeException(
                    'RentCar cache by hash (' . $rentCar->prc_request_hash_key . ') not found. Please refresh search result.'
                );
            }
            if (!$quoteData = RentCarDataParser::findQuoteByToken($dataList, $token, $rentCar->prc_request_hash_key)) {
                throw new \RuntimeException('RentCar quote by token: (' . $token . ') not found', 3);
            }

            $productQuote = RentCarProductQuoteDto::create($rentCar, $quoteData);
            if (!$productQuote->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($productQuote));
            }

            $rentCarQuote = RentCarQuoteDto::create(
                $quoteData,
                $productQuote->pq_id,
                $rentCar->prc_id,
                $rentCar->prc_request_hash_key
            );
            if (!$rentCarQuote->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($rentCarQuote));
            }
        } catch (\Throwable $throwable) {
            Yii::warning(AppHelper::throwableLog($throwable, true), 'RentCarQuoteController:actionAddQuote');
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return [
            'product_id' => $rentCar->prc_product_id,
            'message' => 'Successfully added quote. Rent Car Quote Id: (' . $rentCarQuote->rcq_id . ')'
        ];
    }

    /**
     * @param integer $id
     * @return RentCar
     * @throws NotFoundHttpException
     */
    protected function findRentCar($id): RentCar
    {
        if (($model = RentCar::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The Rent Car not found.');
    }
}
