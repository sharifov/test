<?php

namespace modules\attraction\controllers;

use common\models\Notifications;
use frontend\controllers\FController;
use modules\attraction\AttractionModule;
use modules\attraction\models\Attraction;
use modules\attraction\models\AttractionQuote;
use modules\attraction\models\AttractionQuotePricingCategory;
use modules\attraction\models\forms\AttractionOptionsFrom;
use modules\attraction\models\forms\AvailabilityPaxFrom;
use modules\attraction\models\forms\BookingAnswersForm;
use modules\attraction\models\search\AttractionQuoteSearch;
use modules\attraction\src\services\attractionQuote\AttractionQuotePriceCalculator;
use modules\attraction\src\services\AttractionQuotePdfService;
use modules\attraction\src\useCases\api\searchQuote\AttractionQuoteSearchGuard;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use sales\auth\Auth;
use sales\helpers\app\AppHelper;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use modules\attraction\src\repositories\attraction\AttractionRepository;
use sales\helpers\ErrorsToStringHelper;
use modules\offer\src\entities\offerProduct\OfferProduct;
use modules\offer\src\services\OfferPriceUpdater;
use modules\order\src\services\OrderPriceUpdater;

/**
 * AttractionQuoteController implements the CRUD actions for AttractionQuote model.
 *
 * @property AttractionRepository $attractionRepository
 * @property ProductQuoteRepository $productQuoteRepository
 * @property OrderPriceUpdater $orderPriceUpdater
 * @property OfferPriceUpdater $offerPriceUpdater
 */
class AttractionQuoteController extends FController
{
    /**
     * @var AttractionRepository
     */
    private $attractionRepository;
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;

    private OrderPriceUpdater $orderPriceUpdater;

    private OfferPriceUpdater $offerPriceUpdater;

    public function __construct(
        $id,
        $module,
        AttractionRepository $attractionRepository,
        ProductQuoteRepository $productQuoteRepository,
        OrderPriceUpdater $orderPriceUpdater,
        OfferPriceUpdater $offerPriceUpdater,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->attractionRepository = $attractionRepository;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->orderPriceUpdater = $orderPriceUpdater;
        $this->offerPriceUpdater = $offerPriceUpdater;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all AttractionQuote models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AttractionQuoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Lists all AttractionQuote models.
     * @return mixed
     */
    public function actionSearchAjax()
    {
        $attractionId = (int) Yii::$app->request->get('id');
        $attraction = $this->attractionRepository->find($attractionId);

        $result = [];

        $apiAttractionService = AttractionModule::getInstance()->apiService;

        if ($attraction) {
            try {
                $result = $apiAttractionService->getProductList(AttractionQuoteSearchGuard::guard($attraction));
            } catch (\DomainException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        $attractionsList = $result['productList']['nodes'] ?? [];

        $dataProvider = new ArrayDataProvider([
            'allModels' => [$attractionsList] ?? [],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->renderAjax('search/_search_quotes', [
            'dataProvider' => $dataProvider,
            'attraction'   => $attraction
        ]);
    }

    public function actionAvailabilityListAjax()
    {
        $attractionId = (int) Yii::$app->request->post('atn_id');
        $productKey = (string) Yii::$app->request->post('product_key');

        $result = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $attraction = $this->attractionRepository->find($attractionId);

        $apiAttractionService = AttractionModule::getInstance()->apiService;
        if ($attraction) {
            try {
                $result = $apiAttractionService->getAvailabilityList($productKey, AttractionQuoteSearchGuard::guard($attraction));
            } catch (\DomainException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        $availabilityList = $result['availabilityList']['nodes'] ?? [];

        $dataProvider = new ArrayDataProvider([
            'allModels' => [$availabilityList] ?? [],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->renderAjax('search/_list_availabilities', [
            'dataProvider' => $dataProvider,
            'attraction'   => $attraction,
            'productKey' => $productKey
        ]);
    }

    public function actionCheckAvailabilityAjax()
    {
        $result = [];
        $optionsForm = new AttractionOptionsFrom();

        Yii::$app->response->format = Response::FORMAT_JSON;
        $attractionId = (int) Yii::$app->request->get('atn_id', 0);
        $availabilityKey = (string) Yii::$app->request->post('availability_key', 0);

        $apiAttractionService = AttractionModule::getInstance()->apiService;
        if ($availabilityKey) {
            $result = $apiAttractionService->getAvailability($availabilityKey);
        }

        $availability = $result['availability'];

        //VarDumper::dump($result, 10, true);

        if ($availability) {
            return $this->renderAjax('options', [
                'model' => $optionsForm,
                'availability' => $availability,
                'attractionId' => $attractionId
            ]);
        } else {
            $response['error'] = true;
            $response['message'] = 'Search service dont return any data about tis availability';
            return $response;
        }
    }

    public function actionInputAvailabilityOptions()
    {
        $optionsModel = new AttractionOptionsFrom();
        $availabilityPaxForm = new AvailabilityPaxFrom();
        $attractionId = (int) Yii::$app->request->get('id', 0);

        $optionsModel->load(Yii::$app->request->post());
        $apiAttractionService = AttractionModule::getInstance()->apiService;

        $result = $apiAttractionService->inputOptionsToAvailability($optionsModel);

        if (empty($result['errors'])) {
            $availability = $result['data']['availability'];
            return $this->renderAjax('availability_details', [
                'availability' => $availability,
                'paxForm' => $availabilityPaxForm,
                'attractionId' => $attractionId,
                'model' => $optionsModel,
            ]);
        } else {
            //VarDumper::dump($result, 10, true); die();
            Yii::warning($result['errors'], 'AttractionQuoteController:InputAvailabilityOptions');
            return '<div class="text-center">This availability is not available at this moment check another one<div>';
        }
    }

    public function actionAddQuoteAjax()
    {
        $availabilityPaxModel = new AvailabilityPaxFrom();
        $attractionId = (int) Yii::$app->request->get('id', 0);

        $apiAttractionService = AttractionModule::getInstance()->apiService;

        try {
            if ($availabilityPaxModel->load(Yii::$app->request->post()) && $availabilityPaxModel->validate()) {
                if (!$attractionId) {
                    throw new Exception('Attraction Request param not found', 2);
                }
                $attraction = $this->attractionRepository->find($attractionId);
                $productId = $attraction->atn_product_id;

                $result = $apiAttractionService->inputPriceCategoryToAvailability($availabilityPaxModel);
                $quoteDetails = $result['availability'];

                $invalidPricing = !empty(array_filter($quoteDetails['pricingCategoryList']['nodes'], function ($data) {
                    return $data['isValid'] == false;
                }));

                if (!empty($quoteDetails['pricingCategoryList']['errors']) || $invalidPricing) {
                    $response['error'] = true;
                    $response['message'] = 'invalidPricing';
                    /*if ($invalidPricing && empty($quoteDetails['pricingCategoryList']['errors'])) {
                        $response['message'] = 'invalidPricing';
                    } else {
                        $response['message'] = $quoteDetails['pricingCategoryList']['errors'];
                    }*/
                } else {
                    $productDetails = $apiAttractionService->getProductById($quoteDetails['productId']);

                    if (!$quoteDetails) {
                        throw new Exception('Not found quote - quote key (' . $availabilityPaxModel->availability_id . ')', 7);
                    }

                    $attractionQuote = AttractionQuote::findOrCreateByDataNew($quoteDetails, $attraction, Auth::id());

                    if (!$attractionQuote) {
                        throw new Exception('Not added attraction quote - id:  (' . $availabilityPaxModel->availability_id  . ')', 8);
                    }

                    $attractionQuote->atnq_product_details_json = $productDetails;
                    $attractionQuote->save();

                    Notifications::pub(
                        ['lead-' . $attractionQuote->atnqProductQuote->pqProduct->pr_lead_id],
                        'addedQuote',
                        ['data' => ['productId' => $productId]]
                    );

                    $response['error'] = false;
                    $response['message'] = 'Quote ID: ' . $attractionQuote->atnq_product_quote_id;
                    $response['availabilityID'] = $availabilityPaxModel->availability_id;
                    $response['html'] = $this->renderAjax('quote_details', [
                        'quoteDetails' => $quoteDetails,
                        'productId' => $productId
                    ]);
                }
            } else {
                $response['error'] = true;
                $response['message'] = $this->getParsedErrors($availabilityPaxModel->getErrors());
            }
        } catch (\Throwable $e) {
            Yii::error($e->getMessage() . '; In File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), 'AttractionQuoteController:actionInputPriceCategory:Throwable');
            $response['message'] = 'Internal Server error; Try again letter';
            $response['error'] = true;
        }

        return $this->asJson($response);
    }

    /**
     * Displays a single AttractionQuote model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AttractionQuote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AttractionQuote();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->atnq_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AttractionQuote model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->atnq_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AttractionQuote model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionAjaxBook(): Response
    {
        $id = (int) Yii::$app->request->post('id', 0);
        $apiAttractionService = AttractionModule::getInstance()->apiService;
        $answerModel = new BookingAnswersForm();
        $answerModel->quoteId = $id;

        try {
            $model = $this->findModel($id);
            $resultCreate = $apiAttractionService->createBooking();
            $bookingData = $resultCreate['data'];
            $model->atnq_booking_id = $bookingData['bookingCreate']['id'];
            $model->save();
            $productQuote = $model->atnqProductQuote;
            $productQuote->inProgress(Auth::id());
            $this->productQuoteRepository->save($productQuote);
            $resultAdd = $apiAttractionService->bookingAddAvailability($model->atnq_booking_id, $model->atnq_availability_id);
            $bookingDetails = $apiAttractionService->fetchBooking($model->atnq_booking_id);
            $result = [
                'message' => 'Attraction quote booking opened successful',
                'status' => 1,
                'html' => $this->renderAjax('booking_answers', [
                    'model' => $answerModel,
                    'bookingDetails' => $bookingDetails['data']['booking']
                ])
            ];
        } catch (\Throwable $e) {
            $result = [
                'message' => 'Error. ' . $e->getMessage(),
                'status' => 0,
            ];
        }

        return $this->asJson($result);
    }

    public function actionInputBookAnswers()
    {
        $answerModel = new BookingAnswersForm();
        $answerModel->load(Yii::$app->request->post());
        $apiAttractionService = AttractionModule::getInstance()->apiService;

        $bookingDetails = $apiAttractionService->inputAnswersToBooking($answerModel);
        $result = [
            'message' => '',
            'status' => 1,
            'html' => $this->renderAjax('booking_summary', ['bookingDetails' => $bookingDetails['data']['booking']]),
        ];
        return $this->asJson($result);
    }

    public function actionCheckBookingConfirmation()
    {
        $bookId = Yii::$app->request->post('id', 0);
        $apiAttractionService = AttractionModule::getInstance()->apiService;
        $bookingDetails = $apiAttractionService->fetchBooking($bookId);
        $booking = $bookingDetails['data']['booking'];

        if (!empty($booking['reference']) && strtolower($booking['state']) === 'confirmed') {
            $model = $this->findModel($booking['reference']);
            $productQuote = $model->atnqProductQuote;
            $productQuote->booked(Auth::id());
            $this->productQuoteRepository->save($productQuote);
        }

        $result = [
            'message' => '',
            'status' => 1,
            'productID' => !empty($productQuote->pq_product_id) ? $productQuote->pq_product_id : 0,
            'html' => $this->renderAjax('booking_summary', ['bookingDetails' => $booking]),
        ];
        return $this->asJson($result);
    }

    public function actionAjaxCancelBook(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = (int) Yii::$app->request->post('id', 0);

        try {
            $model = $this->findModel($id);
            $productQuote = $model->atnqProductQuote;
            $productQuote->cancelled(Auth::id());
            $this->productQuoteRepository->save($productQuote);
            $result = [
                'message' => 'Attraction quote canceled successful',
                'status' => 1,
            ];
        } catch (\Throwable $e) {
            $result = [
                'message' => 'Error. ' . $e->getMessage(),
                'status' => 0,
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function actionAjaxCancelBookOld(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = (int) Yii::$app->request->post('id', 0);

        try {
            $model = $this->findModel($id);
            HotelQuoteCancelBookGuard::guard($model);

            /** @var HotelQuoteCancelBookService $cancelBookService */
            $cancelBookService = Yii::$container->get(HotelQuoteCancelBookService::class);
            $resultCancel = $cancelBookService->cancelBook($model);

            $result = [
                'message' => $resultCancel->message,
                'status' => $resultCancel->status,
            ];
        } catch (\Throwable $throwable) {
            $result = [
                'message' => $throwable->getMessage(),
                'status' => 0,
            ];
            \Yii::error(AppHelper::throwableFormatter($throwable), 'Controller:HotelQuoteController:AjaxCancelBook:Throwable');
        }
        return $result;
    }

    public function actionAjaxUpdateAgentMarkup(): Response
    {
        $extraMarkup = Yii::$app->request->post('extra_markup');
        $quoteId = array_key_first($extraMarkup);
        $pricingCategoryKey = key($extraMarkup[$quoteId]);
        $value = $extraMarkup[$quoteId][$pricingCategoryKey];

        if ($quoteId && is_int($quoteId) && $pricingCategoryKey && $value !== null) {
            try {
                if (!$attractionQuote = AttractionQuote::findOne(['atnq_id' => $quoteId])) {
                    throw new \RuntimeException('AttractionQuote not found by id (' . $quoteId . ')');
                }

                if (!$pricingCategory = AttractionQuotePricingCategory::findOne(['atqpc_category_id' => $pricingCategoryKey])) {
                    throw new \RuntimeException('AttractionQuote not found by id (' . $pricingCategoryKey . ')');
                }

                $transaction = \Yii::$app->db->beginTransaction();
                $pricingCategory->atqpc_agent_mark_up = $value;

                if (!$pricingCategory->save()) {
                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($pricingCategory));
                }

                $productQuote = $attractionQuote->atnqProductQuote;
                $prices = (new AttractionQuotePriceCalculator())->calculate($attractionQuote, $productQuote->pq_origin_currency_rate);
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
                Yii::error($e->getTraceAsString(), 'AttractionQuoteController::actionAjaxUpdateAgentMarkup');
            }

            return $this->asJson(['output' => $value]);
        }

        throw new BadRequestHttpException();
    }

    public function actionAjaxFileGenerate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = (int) Yii::$app->request->post('id', 0);

        $result = ['status' => 0, 'message' => ''];

        try {
            $attractionQuote = $this->findModel($id);

            if (!$attractionQuote->isBooking()) {
                throw new \DomainException('Quote should have Booked status.');
            }
            $attractionQuotePdfService = new AttractionQuotePdfService($attractionQuote);
            $attractionQuotePdfService->setProductQuoteId($attractionQuote->atnq_product_quote_id);
            if ($attractionQuotePdfService->processingFile()) {
                $result['status'] = 1;
                $result['message'] = 'Document have been successfully generated';
            }
        } catch (\Throwable $throwable) {
            $result['message'] = $throwable->getMessage();
            \Yii::error(AppHelper::throwableFormatter($throwable), 'AttractionQuoteController:actionAjaxFileGenerate');
        }
        return $result;
    }

    public function actionAjaxQuoteDetails(): string
    {
        $productQuoteId = Yii::$app->request->get('id');
        $productQuote = $this->productQuoteRepository->find($productQuoteId);

        return $this->renderAjax('partial/_quote_view_details', [
            'attractionQuote' => $productQuote->attractionQuote
        ]);
    }

    /**
     * Finds the HotelQuote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AttractionQuote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): AttractionQuote
    {
        if (($model = AttractionQuote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested Attraction does not exist.');
    }
}
