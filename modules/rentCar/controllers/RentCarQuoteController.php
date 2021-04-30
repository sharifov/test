<?php

namespace modules\rentCar\controllers;

use common\models\Notifications;
use frontend\controllers\FController;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\offer\src\services\OfferPriceUpdater;
use modules\order\src\events\OrderFileGeneratedEvent;
use modules\order\src\services\OrderPriceUpdater;
use modules\rentCar\components\ApiRentCarService;
use modules\rentCar\RentCarModule;
use modules\rentCar\src\entity\dto\RentCarProductQuoteDto;
use modules\rentCar\src\entity\dto\RentCarQuoteDto;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\forms\RentCarSearchForm;
use modules\rentCar\src\helpers\RentCarDataParser;
use modules\rentCar\src\helpers\RentCarQuoteHelper;
use modules\rentCar\src\repositories\rentCar\RentCarQuoteRepository;
use modules\rentCar\src\services\RentCarQuoteBookGuard;
use modules\rentCar\src\services\RentCarQuoteBookService;
use modules\rentCar\src\services\RentCarQuoteCancelBookService;
use modules\rentCar\src\services\RentCarQuotePdfService;
use modules\rentCar\src\services\RentCarQuotePriceCalculator;
use sales\auth\Auth;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\product\src\entities\productQuote\ProductQuoteRepository;

/**
 * Class RentCarQuoteController
 *
 * @property OrderPriceUpdater $orderPriceUpdater
 * @property OfferPriceUpdater $offerPriceUpdater
 * @property ProductQuoteRepository $productQuoteRepository
 */
class RentCarQuoteController extends FController
{
    private OrderPriceUpdater $orderPriceUpdater;
    private OfferPriceUpdater $offerPriceUpdater;
    private $productQuoteRepository;

    public function __construct(
        $id,
        $module,
        OrderPriceUpdater $orderPriceUpdater,
        OfferPriceUpdater $offerPriceUpdater,
        ProductQuoteRepository $productQuoteRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->orderPriceUpdater = $orderPriceUpdater;
        $this->offerPriceUpdater = $offerPriceUpdater;
        $this->productQuoteRepository = $productQuoteRepository;
    }

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
            $dataList = \Yii::$app->cacheFile->get($rentCar->prc_request_hash_key);

            if ($dataList === false) {
                $result = $apiRentCarService->search(
                    $rentCar->prc_pick_up_code,
                    $rentCar->prc_pick_up_date,
                    $rentCar->prc_pick_up_time,
                    $rentCar->prc_drop_off_time,
                    $rentCar->prc_drop_off_code,
                    $rentCar->prc_drop_off_date,
                    $rentCar->prc_request_hash_key
                );

                if (!$dataList = ArrayHelper::getValue($result, 'data.result_list')) {
                    throw new \DomainException('DataList not found in search response');
                }
                \Yii::$app->cacheFile->set($rentCar->prc_request_hash_key, $dataList, 300);
            }

            $dataProvider = new ArrayDataProvider([
                'allModels' => $dataList,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        } catch (\Throwable $throwable) {
            Yii::warning(AppHelper::throwableLog($throwable), 'RentCarQuoteController:actionSearchAjax');
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

        $transaction = \Yii::$app->db->beginTransaction();
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
            $productQuote = RentCarProductQuoteDto::create($rentCar, $quoteData, Auth::id());
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

            $prices = (new RentCarQuotePriceCalculator())->calculate($rentCarQuote, $productQuote->pq_origin_currency_rate);
            $productQuote->updatePrices(
                $prices['originPrice'],
                $prices['appMarkup'],
                $prices['agentMarkup']
            );
            if (!$productQuote->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($productQuote));
            }

            Notifications::pub(
                ['lead-' . $productQuote->pqProduct->pr_lead_id],
                'addedQuote',
                ['data' => ['productId' => $productQuote->pq_product_id]]
            );

            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::warning(AppHelper::throwableLog($throwable), 'RentCarQuoteController:actionAddQuote');
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return [
            'product_id' => $rentCar->prc_product_id,
            'message' => 'Successfully added quote. Rent Car Quote Id: (' . $rentCarQuote->rcq_id . ')'
        ];
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionContractRequest(): array
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $referenceId = Yii::$app->request->post('referenceId');
        $rentCarId = Yii::$app->request->post('requestId');
        $result = ['status' => 0, 'message' => ''];

        try {
            if (!$rentCarId) {
                throw new \RuntimeException('RentCarId param not found');
            }
            if (!$referenceId) {
                throw new \RuntimeException('Reference param not found');
            }
            $rentCar = $this->findRentCar($rentCarId);
            $apiRentCarService = RentCarModule::getInstance()->apiService;
            $dataResult = \Yii::$app->cacheFile->get($referenceId);

            if ($dataResult === false) {
                $dataResult = $apiRentCarService->contractRequest($referenceId, $rentCar->prc_request_hash_key);
                \Yii::$app->cacheFile->set($referenceId, $dataResult, 60);
            }

            if ($dataResult['error'] === false) {
                if (
                    ($contractStatus = ArrayHelper::getValue($dataResult, 'data.contract_status')) &&
                    strtoupper((string) $contractStatus) === 'SUCCESS'
                ) {
                    $result['status'] = 1;
                    $result['message'] = 'Contract request success';
                } else {
                    $result['message'] = $contractStatus ?? 'Contract request fail';
                }
            } else {
                $result['message'] = $dataResult['error'];
            }
        } catch (\Throwable $throwable) {
            Yii::warning(AppHelper::throwableLog($throwable), 'RentCarQuoteController:actionContractRequest');
            $result['message'] = $throwable->getMessage();
        }
        return $result;
    }

    public function actionFileGenerate(): array
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $rentCarQuoteId = Yii::$app->request->post('id');
        $result = ['status' => 0, 'message' => ''];

        try {
            $rentCarQuote = $this->findRentCarQuote($rentCarQuoteId);
            if (!$rentCarQuote->rcqProductQuote->isBooked()) {
                throw new \DomainException('Quote should have Booked status.');
            }
            $rentCarQuotePdfService = new RentCarQuotePdfService($rentCarQuote);
            $rentCarQuotePdfService->setProductQuoteId($rentCarQuote->rcqProductQuote->pq_id);
            if ($rentCarQuotePdfService->processingFile()) {
                $result['status'] = 1;
                $result['message'] = 'Document have been successfully generated';
            }
        } catch (\Throwable $throwable) {
            $throwableLog = AppHelper::throwableLog($throwable);
            Yii::warning(
                ArrayHelper::merge(
                    $throwableLog,
                    ['productQuoteId' => isset($rentCarQuote) ? $rentCarQuote->rcqProductQuote->pq_id : null]
                ),
                'RentCarQuoteController:actionFileGenerate'
            );
            $result['message'] = $throwable->getMessage();
        }
        return $result;
    }

    public function actionBook()
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $rentCarQuoteId = Yii::$app->request->post('id');
        $result = ['status' => 0, 'message' => '', 'data' => []];

        try {
            if (!$rentCarQuoteId) {
                throw new \RuntimeException('RentCarQuoteId param not found');
            }
            $rentCarQuote = $this->findRentCarQuote($rentCarQuoteId);
            RentCarQuoteBookGuard::guard($rentCarQuote);

            if ($bookingId = RentCarQuoteBookService::book($rentCarQuote, Auth::id())) {
                Notifications::pub(
                    ['lead-' . ArrayHelper::getValue($rentCarQuote, 'rcqProductQuote.pqProduct.pr_lead_id')],
                    'quoteBooked',
                    ['data' => ['productId' => ArrayHelper::getValue($rentCarQuote, 'rcqProductQuote.pq_product_id')]]
                );
                $result['message'] = 'Success. BookingId (' . $bookingId . ')';
                $result['status'] = 1;
            }
        } catch (\Throwable $throwable) {
            Yii::warning(AppHelper::throwableLog($throwable), 'RentCarQuoteController:actionContractRequest');
            $result['message'] = $throwable->getMessage();
        }
        return $result;
    }

    public function actionCancelBook(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = (int) Yii::$app->request->post('id', 0);

        try {
            $model = $this->findRentCarQuote($id);
            $cancelBookService = Yii::createObject(RentCarQuoteCancelBookService::class);
            $cancelBookService::cancelBook($model, Auth::id());

            $result = [
                'message' => 'Booking canceled successfully',
                'status' => 1,
            ];
        } catch (\Throwable $throwable) {
            $result = [
                'message' => $throwable->getMessage(),
                'status' => 0,
            ];
            \Yii::error(AppHelper::throwableLog($throwable), 'RentCarQuoteController:actionCancelBook');
        }
        return $result;
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
                $prices = (new RentCarQuotePriceCalculator())->calculate($rentCarQuote, $productQuote->pq_origin_currency_rate);
                $productQuote->updatePrices(
                    $prices['originPrice'],
                    $prices['appMarkup'],
                    $prices['agentMarkup']
                );

                if (!$productQuote->save()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($productQuote));
                }
                $transaction->commit();

                if ($productQuote->pq_order_id) {
                    $this->orderPriceUpdater->update($productQuote->pq_order_id);
                }

                $offers = OfferProduct::find()->select(['op_offer_id'])->andWhere(['op_product_quote_id' => $productQuote->pq_id])->column();
                foreach ($offers as $offerId) {
                    $this->offerPriceUpdater->update($offerId);
                }
                $leadId = $productQuote->pqProduct->pr_lead_id ?? null;
                if ($leadId) {
                    Notifications::pub(
                        ['lead-' . $leadId],
                        'reloadOrders',
                        ['data' => []]
                    );
                    Notifications::pub(
                        ['lead-' . $leadId],
                        'reloadOffers',
                        ['data' => []]
                    );
                }
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

    public function actionAjaxQuoteDetails(): string
    {
        $productQuoteId = Yii::$app->request->get('id');
        $productQuote = $this->productQuoteRepository->find($productQuoteId);

        return $this->renderAjax('partial/_quote_view_details', [
            'rentCarQuote' => $productQuote->rentCarQuote
        ]);
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

    /**
     * @param $id
     * @return RentCarQuote
     * @throws NotFoundHttpException
     */
    protected function findRentCarQuote($id): RentCarQuote
    {
        if (($model = RentCarQuote::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The Rent Car Quote not found.');
    }
}
