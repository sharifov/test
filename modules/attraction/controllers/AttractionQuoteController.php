<?php

namespace modules\attraction\controllers;

use common\models\Notifications;
use frontend\controllers\FController;
use modules\attraction\AttractionModule;
use modules\attraction\models\Attraction;
use modules\attraction\models\AttractionQuote;
use modules\attraction\models\search\AttractionQuoteSearch;
use modules\attraction\src\services\AttractionQuotePdfService;
use modules\hotel\models\Hotel;
use modules\hotel\models\HotelList;
use modules\hotel\models\HotelQuote;
use modules\hotel\src\entities\hotelQuoteRoom\HotelQuoteRoomRepository;
use modules\hotel\src\repositories\hotel\HotelRepository;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteBookGuard;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteCancelBookGuard;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteCheckRateService;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteBookService;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteCancelBookService;
use modules\attraction\src\useCases\api\searchQuote\AttractionQuoteSearchGuard;
use modules\attraction\src\useCases\api\searchQuote\HotelQuoteSearchService;
use modules\hotel\src\useCases\quote\HotelQuoteManageService;
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

/**
 * AttractionQuoteController implements the CRUD actions for AttractionQuote model.
 *
 * @property HotelRepository $hotelRepository
 * @property AttractionRepository $attractionRepository
 * @property HotelQuoteSearchService $hotelQuoteSearchService
 * @property HotelQuoteRoomRepository $hotelQuoteRoomRepository
 * @property HotelQuoteManageService $hotelQuoteManageService
 * @property ProductQuoteRepository $productQuoteRepository
 */
class AttractionQuoteController extends FController
{
    /**
     * @var HotelRepository
     */
    private $hotelRepository;
    /**
     * @var AttractionRepository
     */
    private $attractionRepository;
    /**
     * @var HotelQuoteSearchService
     */
    private $hotelQuoteSearchService;
    /**
     * @var HotelQuoteRoomRepository
     */
    private $hotelQuoteRoomRepository;
    /**
     * @var HotelQuoteManageService
     */
    private $hotelQuoteManageService;
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;

    public function __construct(
        $id,
        $module,
        HotelQuoteSearchService $hotelQuoteSearchService,
        HotelRepository $hotelRepository,
        AttractionRepository $attractionRepository,
        HotelQuoteManageService $hotelQuoteManageService,
        HotelQuoteRoomRepository $hotelQuoteRoomRepository,
        ProductQuoteRepository $productQuoteRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);

        $this->hotelQuoteSearchService = $hotelQuoteSearchService;
        $this->hotelRepository = $hotelRepository;
        $this->attractionRepository = $attractionRepository;
        $this->hotelQuoteRoomRepository = $hotelQuoteRoomRepository;
        $this->hotelQuoteManageService = $hotelQuoteManageService;
        $this->productQuoteRepository = $productQuoteRepository;
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
            'hotelSearch'   => $attraction
        ]);
    }

    public function actionAvailabilityListAjax()
    {
        $attractionId = (int) Yii::$app->request->post('atn_id');
        $attractionKey = (string) Yii::$app->request->post('attraction_key');

        $result = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $attraction = $this->attractionRepository->find($attractionId);

        $apiAttractionService = AttractionModule::getInstance()->apiService;
        if ($attraction) {
            try {
                $result = $apiAttractionService->getAvailabilityList($attractionKey, AttractionQuoteSearchGuard::guard($attraction));
            } catch (\DomainException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        $availabilityList = $result['availabilityList']['nodes'] ?? [];

        foreach ($availabilityList as $key => $element) {
            $availabilityList[$key]['presentation_product_id'] = $attractionKey; //for presentation only is temp
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => [$availabilityList] ?? [],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->renderAjax('search/_list_availabilities', [
            'dataProvider' => $dataProvider,
            'attractionSearch'   => $attraction
        ]);
    }


    public function actionAddAjax(): array
    {
        $attractionId = (int) Yii::$app->request->get('atn_id');
        $quoteKey = (string) Yii::$app->request->post('quote_key');
        $date = (string) Yii::$app->request->post('date'); // only for presentation

        $productId = 0;

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            if (!$attractionId) {
                throw new Exception('Attraction Request param not found', 2);
            }

            /*if (!$hotelCode) {
                throw new Exception('Hotel Code param not found', 3);
            }*/

            if (!$quoteKey) {
                throw new Exception('Quote key param not found', 4);
            }

            $attraction = $this->attractionRepository->find($attractionId);
            $productId = $attraction->atn_product_id;

            //$result = $attraction->getSearchData();
            $quoteData = $attraction->getSearchData($quoteKey);

            //$quoteData = Attraction::getAttractionQuoteDataByKey($result, $quoteKey);

            if (!$quoteData) {
                throw new Exception('Not found quote - quote key (' . $quoteKey . ')', 7);
            }

            //$currency = $hotelData['currency'] ?? 'USD';

            $attractionQuote = AttractionQuote::findOrCreateByData($quoteData, $attraction, $date, Auth::id(), $currency = 'USD');

            if (!$attractionQuote) {
                throw new Exception('Not added attraction quote - id:  (' . $quoteKey . ')', 8);
            }

            Notifications::pub(
                ['lead-' . $attractionQuote->atnqProductQuote->pqProduct->pr_lead_id],
                'addedQuote',
                ['data' => ['productId' => $attractionQuote->atnqProductQuote->pq_product_id]]
            );
        } catch (\Throwable $throwable) {
            Yii::warning(VarDumper::dumpAsString($throwable->getTraceAsString()), 'app');
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return [
            'product_id' => $productId,
            'message' => 'Product "' . Html::encode($attraction->atnProduct->pr_name) . '", Attraction Quote Id: (' . $attractionQuote->atnq_id . ')'
        ];
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

    /**
     * @return array
     */
    public function actionAjaxBookOld(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = (int) Yii::$app->request->post('id', 0);
        $checkRate = Yii::$app->request->post('check_rate', 1);
        $result = ['status' => 0, 'message' => '', 'data' => []];

        try {
            $model = $this->findModel($id);
            HotelQuoteBookGuard::guard($model);

            /** @var HotelQuoteBookService $bookService */
            $bookService = Yii::$container->get(HotelQuoteBookService::class);

            if ($checkRate) {
                /** @var HotelQuoteCheckRateService $checkRateService */
                $checkRateService = Yii::$container->get(HotelQuoteCheckRateService::class);
                $checkResult = $checkRateService->checkRateByHotelQuote($model);

                if ($checkResult->status) {
                    $bookService->book($model);
                    $result['status'] = $bookService->status;
                    $result['message'] = $bookService->message;
                } else {
                    $result['status'] = $checkResult->status;
                    $result['message'] = $checkResult->message;
                }
            } else {
                $bookService->book($model);
                $result['status'] = $bookService->status;
                $result['message'] = $bookService->message;
            }
        } catch (\Throwable $throwable) {
            $result['message'] = $throwable->getMessage();
            \Yii::error(AppHelper::throwableFormatter($throwable), 'Controller:HotelQuoteController:AjaxBook:Throwable');
        }
        return $result;
    }

    public function actionAjaxBook(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = (int) Yii::$app->request->post('id', 0);

        try {
            $model = $this->findModel($id);
            $model->atnq_booking_id = strtoupper(substr(md5(mt_rand()), 0, 7));
            $model->save();
            $productQuote = $model->atnqProductQuote;
            $productQuote->booked(Auth::id());
            $this->productQuoteRepository->save($productQuote);
            $result = [
                'message' => 'Attraction quote booked successful',
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

    /**
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionAjaxUpdateAgentMarkup(): Response
    {
        $extraMarkup = Yii::$app->request->post('extra_markup');

        $hotelQuoteRoomId = array_key_first($extraMarkup);
        $value = $extraMarkup[$hotelQuoteRoomId];

        if ($hotelQuoteRoomId && $value !== null) {
            try {
                $hotelQuoteRoom = $this->hotelQuoteRoomRepository->find($hotelQuoteRoomId);

                $this->hotelQuoteManageService->updateAgentMarkup($hotelQuoteRoom, $value);
                $leadId = $hotelQuoteRoom->hqrHotelQuote->hqProductQuote->pqProduct->pr_lead_id ?? null;
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
                return $this->asJson(['message' => $e->getMessage()]);
            } catch (\Throwable $e) {
                Yii::error($e->getTraceAsString(), 'HotelQuoteController::actionAjaxUpdateAgentMarkup::Throwable');
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

            if (AttractionQuotePdfService::processingFile($attractionQuote)) {
                $result['status'] = 1;
                $result['message'] = 'Document have been successfully generated';
            }
        } catch (\Throwable $throwable) {
            $result['message'] = $throwable->getMessage();
            \Yii::error(AppHelper::throwableFormatter($throwable), 'AttractionQuoteController:actionAjaxFileGenerate');
        }
        return $result;
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
