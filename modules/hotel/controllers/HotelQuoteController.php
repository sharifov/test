<?php

namespace modules\hotel\controllers;

use frontend\controllers\FController;
use modules\hotel\models\Hotel;
use modules\hotel\models\HotelList;
use modules\hotel\models\HotelQuote;
use modules\hotel\models\search\HotelQuoteSearch;
use modules\hotel\src\repositories\hotel\HotelRepository;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteBookGuard;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteCancelBookGuard;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteCheckRateService;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteBookService;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteCancelBookService;
use modules\hotel\src\useCases\api\searchQuote\HotelQuoteSearchGuard;
use modules\hotel\src\useCases\api\searchQuote\HotelQuoteSearchService;

use sales\helpers\app\AppHelper;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * HotelQuoteController implements the CRUD actions for HotelQuote model.
 *
 * @property HotelRepository $hotelRepository
 * @property HotelQuoteSearchService $hotelQuoteSearchService
 */
class HotelQuoteController extends FController
{
	/**
	 * @var HotelRepository
	 */
	private $hotelRepository;
	/**
	 * @var HotelQuoteSearchService
	 */
	private $hotelQuoteSearchService;

	public function __construct($id, $module, HotelQuoteSearchService $hotelQuoteSearchService, HotelRepository $hotelRepository, $config = [])
	{
		parent::__construct($id, $module, $config);

		$this->hotelQuoteSearchService = $hotelQuoteSearchService;
		$this->hotelRepository = $hotelRepository;
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

        $hotelId = (int) Yii::$app->request->get('id');
        $hotel = $this->hotelRepository->find($hotelId);

        $result = [];

        if ($hotel) {
			try {
            	$result = $this->hotelQuoteSearchService->search(HotelQuoteSearchGuard::guard($hotel));
			} catch (\DomainException $e) {
				Yii::$app->session->setFlash('error', $e->getMessage());
			}
        }
        $hotelList = $result['hotels'] ?? [];

        $dataProvider = new ArrayDataProvider([
            'allModels' => $hotelList,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['ranking', 'name', 's2C'],
            ],
        ]);

        return $this->renderAjax('search/_search_quotes', [
            'dataProvider' => $dataProvider,
            'hotelSearch'   => $hotel
        ]);
    }


    public function actionAddAjax(): array
    {
        $hotelId = (int) Yii::$app->request->get('ph_id');
        $hotelCode = (int) Yii::$app->request->post('hotel_code');
        $quoteKey = (int) Yii::$app->request->post('quote_key');
        $productId = 0;

        Yii::$app->response->format = Response::FORMAT_JSON;

        try {

            if (!$hotelId) {
                throw new Exception('Hotel Request param not found', 2);
            }

            if (!$hotelCode) {
                throw new Exception('Hotel Code param not found', 3);
            }

            if (!$quoteKey) {
                throw new Exception('Quote key param not found', 4);
            }

            $modelHotel = Hotel::findOne($hotelId);
            if (!$modelHotel) {
                throw new Exception('Hotel Request ('.$hotelId.') not found in DB', 5);
            }

            $productId = $modelHotel->ph_product_id;

            $result = $modelHotel->getSearchData();

            $hotelData = Hotel::getHotelDataByCode($result, $hotelCode);
            $quoteData = Hotel::getHotelQuoteDataByKey($result, $hotelCode, $quoteKey);

            //VarDumper::dump($hotelData); exit;
            //VarDumper::dump($roomData); exit;

            if (!$hotelData) {
                throw new Exception('Not found quote - hotel code ('.$hotelCode.')', 6);
            }

            if (!$quoteData) {
                throw new Exception('Not found quote - quote key ('.$quoteKey.')', 7);
            }

            $hotelModel = HotelList::findOrCreateByData($hotelData);

            $currency = $hotelData['currency'] ?? 'USD';

            $hotelQuote = HotelQuote::findOrCreateByData($quoteData, $hotelModel, $modelHotel, $currency);

            if (!$hotelQuote) {
                throw new Exception('Not added hotel quote - hotel code (' . $hotelCode . ') room key (' . $quoteKey . ')', 8);
            }

            //$hotelList = $result['hotels'] ?? [];


        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return [
            'product_id' => $productId,
            'message' => 'Successfully added quote. Hotel "'.Html::encode($hotelData['name']).'", Hotel Quote Id: (' . $hotelQuote->hq_id . ')'
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
     * @return Response
     */
    public function actionAjaxBook()
    {
        $id = (int) Yii::$app->request->post('id', 0);
        $checkRate = Yii::$app->request->post('check_rate', 1);
        $result = ['status' => 0, 'message' => '', 'data' => []];

        try {
            $model = HotelQuoteBookGuard::guard($id);
            $bookService = Yii::$container->get(HotelQuoteBookService::class);

            if ($checkRate) {
                $checkResult = (new HotelQuoteCheckRateService)->checkRate($model);

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
            \Yii::error(AppHelper::throwableFormatter($throwable), self::class . ':' . __FUNCTION__ . ':Throwable');
        }
        return $this->asJson($result);
    }

    /**
     * @return Response
     */
    public function actionAjaxCancelBook()
    {
        $id = (int) Yii::$app->request->post('id', 0);

        try {
            $model = HotelQuoteCancelBookGuard::guard($id);
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
            \Yii::error(AppHelper::throwableFormatter($throwable), self::class . ':' . __FUNCTION__ . ':Throwable');
        }
        return $this->asJson($result);
    }

    /**
     * Finds the HotelQuote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HotelQuote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HotelQuote::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
