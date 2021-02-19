<?php

namespace modules\attraction\controllers;

use common\components\CommunicationService;
use common\models\Notifications;
use frontend\controllers\FController;
use modules\attraction\models\Attraction;
use modules\attraction\models\AttractionQuote;
use modules\hotel\models\Hotel;
use modules\hotel\models\HotelList;
use modules\hotel\models\HotelQuote;
use modules\hotel\models\search\HotelQuoteSearch;
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
 * HotelQuoteController implements the CRUD actions for HotelQuote model.
 *
 * @property HotelRepository $hotelRepository
 * @property AttractionRepository $attractionRepository
 * @property HotelQuoteSearchService $hotelQuoteSearchService
 * @property HotelQuoteRoomRepository $hotelQuoteRoomRepository
 * @property HotelQuoteManageService $hotelQuoteManageService
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

    public function __construct(
        $id,
        $module,
        HotelQuoteSearchService $hotelQuoteSearchService,
        HotelRepository $hotelRepository,
        AttractionRepository $attractionRepository,
        HotelQuoteManageService $hotelQuoteManageService,
        HotelQuoteRoomRepository $hotelQuoteRoomRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);

        $this->hotelQuoteSearchService = $hotelQuoteSearchService;
        $this->hotelRepository = $hotelRepository;
        $this->attractionRepository = $attractionRepository;
        $this->hotelQuoteRoomRepository = $hotelQuoteRoomRepository;
        $this->hotelQuoteManageService = $hotelQuoteManageService;
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
     * Lists all HotelQuote models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HotelQuoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Lists all HotelQuote models.
     * @return mixed
     */
    public function actionSearchAjax()
    {
        $attractionId = (int) Yii::$app->request->get('id');
        $attraction = $this->attractionRepository->find($attractionId);

        $result = [];

        /** @var CommunicationService $communication */
        $communication = Yii::$app->communication;

        if ($attraction) {
            try {
                $result = $communication->getAttractionQuotes(AttractionQuoteSearchGuard::guard($attraction));
                //$result = $this->hotelQuoteSearchService->search(HotelQuoteSearchGuard::guard($hotel));
            } catch (\DomainException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        $hotelList = $result['data']['activityGroups'][0] ?? [];
//        echo '<pre>';
//        VarDumper::dump($hotelList['activityTiles']);die;
        $dataProvider = new ArrayDataProvider([
            //'allModels' => [[$hotelList]],
            'allModels' => [$hotelList['activityTiles'] ?? []],
            'pagination' => [
                'pageSize' => 10,
            ],
            /*'sort' => [
                'attributes' => ['ranking', 'name', 's2C'],
            ],*/
        ]);

        return $this->renderAjax('search/_search_quotes', [
            'dataProvider' => $dataProvider,
            'hotelSearch'   => $attraction
        ]);
    }


    public function actionAddAjax(): array
    {
        $attractionId = (int) Yii::$app->request->get('atn_id');
        //$hotelCode = (int) Yii::$app->request->post('hotel_code');
        $quoteKey = (int) Yii::$app->request->post('quote_key');
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

            $result = $attraction->getSearchData();

            //$hotelData = Hotel::getHotelDataByCode($result, $hotelCode);
            $quoteData = Attraction::getHotelQuoteDataByKey($result, $quoteKey);

           /* if (!$hotelData) {
                throw new Exception('Not found quote - hotel code (' . $hotelCode . ')', 6);
            }*/

            if (!$quoteData) {
                throw new Exception('Not found quote - quote key (' . $quoteKey . ')', 7);
            }

            //$hotelModel = HotelList::findOrCreateByData($hotelData);

            //$currency = $hotelData['currency'] ?? 'USD';

            $attractionQuote = AttractionQuote::findOrCreateByData($quoteData, $attraction, $currency = 'USD');

            if (!$attractionQuote) {
                throw new Exception('Not added attraction quote - id:  (' . $quoteKey . ')', 8);
            }

            Notifications::pub(
                ['lead-' . $attractionQuote->atnqProductQuote->pqProduct->pr_lead_id],
                'addedQuote',
                ['data' => ['productId' => $attractionQuote->atnqProductQuote->pq_product_id]]
            );

            //$hotelList = $result['hotels'] ?? [];
        } catch (\Throwable $throwable) {
            Yii::warning(VarDumper::dumpAsString($throwable->getTraceAsString()), 'app');
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return [
            'product_id' => $productId,
            'message' => 'Successfully added quote. Hotel "' . Html::encode($attraction->atnProduct->pr_name) . '", Attraction Quote Id: (' . $attractionQuote->atnq_id . ')'
        ];
    }

    /**
     * Displays a single HotelQuote model.
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
     * Creates a new HotelQuote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HotelQuote();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->hq_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing HotelQuote model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->hq_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing HotelQuote model.
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
    public function actionAjaxBook(): array
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
                $checkResult = $checkRateService->checkRate($model);

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

    /**
     * @return array
     */
    public function actionAjaxCancelBook(): array
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
            } catch (\RuntimeException $e) {
                return $this->asJson(['message' => $e->getMessage()]);
            } catch (\Throwable $e) {
                Yii::error($e->getTraceAsString(), 'HotelQuoteController::actionAjaxUpdateAgentMarkup::Throwable');
            }

            return $this->asJson(['output' => $value]);
        }

        throw new BadRequestHttpException();
    }

    /**
     * Finds the HotelQuote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HotelQuote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): HotelQuote
    {
        if (($model = HotelQuote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested HotelQuote does not exist.');
    }
}