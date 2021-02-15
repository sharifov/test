<?php

namespace modules\rentCar\controllers;

use frontend\controllers\FController;
use modules\rentCar\components\ApiRentCarService;
use modules\rentCar\RentCarModule;
use modules\rentCar\src\entity\dto\RentCarProductQuoteDto;
use modules\rentCar\src\entity\dto\RentCarQuoteDto;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\forms\RentCarSearchForm;
use modules\rentCar\src\helpers\RentCarDataParser;
use modules\rentCar\src\helpers\RentCarQuoteHelper;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
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
        try {
            $rentCarId = (int) Yii::$app->request->get('id');
            $rentCar = $this->findRentCar($rentCarId);

            $form = new RentCarSearchForm($rentCar);
            if (!$form->validate()) {
                throw new \RuntimeException($form->getErrorSummary(false)[0]);
            }

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
        } catch (\Throwable $throwable) {
            Yii::warning(AppHelper::throwableLog($throwable, true), 'RentCarQuoteController:actionAddQuote');
            $error  = $throwable->getMessage();
        }

        return $this->renderAjax('search/_search_quotes', [
            'dataProvider' => $dataProvider ?? null,
            'rentCar' => $rentCar ?? null,
            'error' => $error ?? null,
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

            $transaction = \Yii::$app->db->beginTransaction();
            $productQuote = RentCarProductQuoteDto::create($rentCar, $quoteData);
            if (!$productQuote->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($productQuote));
            }

            $rentCarQuote = RentCarQuoteDto::create(
                $quoteData,
                $productQuote->pq_id,
                $rentCar
            );
            if (!$rentCarQuote->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($rentCarQuote));
            }

            $productQuote = RentCarProductQuoteDto::priceUpdate($productQuote, $rentCarQuote);
            if (!$productQuote->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($productQuote));
            }

            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::warning(AppHelper::throwableLog($throwable, true), 'RentCarQuoteController:actionAddQuote');
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return [
            'product_id' => $rentCar->prc_product_id,
            'message' => 'Successfully added quote. Rent Car Quote Id: (' . $rentCarQuote->rcq_id . ')'
        ];
    }

    public function actionAjaxUpdateAgentMarkup(): Response
    {
        $extraMarkup = Yii::$app->request->post('extra_markup');

        $quoteId = array_key_first($extraMarkup);
        $value = $extraMarkup[$quoteId];

        if ($quoteId && is_int($quoteId) && $value !== null) {
            try {
                if (!$rentCarQuote = RentCarQuote::findOne(['rcq_id' => $quoteId])) {
                    throw new \RuntimeException('RentCarQuote not found by id (' . $quoteId . ')');
                }
                $transaction = \Yii::$app->db->beginTransaction();
                $rentCarQuote->rcq_agent_mark_up = $value;
                if (!$rentCarQuote->save()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($rentCarQuote));
                }

                $productQuote = $rentCarQuote->rcqProductQuote;
                $productQuote = RentCarProductQuoteDto::priceUpdate($productQuote, $rentCarQuote);
                $productQuote->recalculateProfitAmount();
                if (!$productQuote->save()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($productQuote));
                }
                $transaction->commit();
            } catch (\RuntimeException $e) {
                $transaction->rollBack();
                return $this->asJson(['message' => $e->getMessage()]);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error($e->getTraceAsString(), 'RentCarQuoteController::actionAjaxUpdateAgentMarkup');
            }

            return $this->asJson(['output' => $value]);
        }

        throw new BadRequestHttpException();
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
