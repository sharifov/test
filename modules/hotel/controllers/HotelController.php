<?php

namespace modules\hotel\controllers;

use modules\hotel\src\useCases\request\update\HotelUpdateRequestForm;
use modules\hotel\src\useCases\request\update\HotelRequestUpdateService;
use modules\product\src\entities\productQuoteOrigin\service\ProductQuoteOriginService;
use modules\product\src\entities\productType\ProductType;
use modules\hotel\src\helpers\HotelFormatHelper;
use modules\product\src\services\ProductCloneService;
use src\auth\Auth;
use src\repositories\lead\LeadRepository;
use src\repositories\product\ProductQuoteRepository;
use Yii;
use modules\hotel\models\Hotel;
use modules\hotel\models\search\HotelSearch;
use frontend\controllers\FController;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * HotelController implements the CRUD actions for Hotel model.
 *
 * @property HotelRequestUpdateService $hotelRequestUpdateService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property LeadRepository $leadRepository
 * @property ProductCloneService $productCloneService
 * @property ProductQuoteOriginService $productQuoteOriginService
 */
class HotelController extends FController
{
    private HotelRequestUpdateService $hotelRequestUpdateService;
    private ProductQuoteRepository $productQuoteRepository;
    private LeadRepository $leadRepository;
    private ProductCloneService $productCloneService;
    private ProductQuoteOriginService $productQuoteOriginService;

    public function __construct(
        $id,
        $module,
        HotelRequestUpdateService $hotelRequestUpdateService,
        ProductQuoteRepository $productQuoteRepository,
        LeadRepository $leadRepository,
        ProductCloneService $productCloneService,
        ProductQuoteOriginService $productQuoteOriginService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->hotelRequestUpdateService = $hotelRequestUpdateService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->leadRepository = $leadRepository;
        $this->productCloneService = $productCloneService;
        $this->productQuoteOriginService = $productQuoteOriginService;
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
     * Lists all Hotel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HotelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Hotel model.
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
     * Creates a new Hotel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Hotel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ph_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Hotel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->ph_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdateAjax()
    {
        $id = Yii::$app->request->get('id');

        try {
            $hotel = $this->findModel($id);
        } catch (\Throwable $throwable) {
            return '<script>alert("' . $throwable->getMessage() . '")</script>';
        }

        $form = new HotelUpdateRequestForm($hotel);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $out = '<script>$("#modal-sm").modal("hide"); pjaxReload({container: "#pjax-product-search-' . $hotel->ph_product_id . '"});';
            try {
                $this->hotelRequestUpdateService->update($form);
                $out .= 'createNotifyByObject({title: "Hotel update request", type: "success", text: "Success" , hide: true});';
            } catch (\DomainException $e) {
                $out .= 'createNotifyByObject({title: "Hotel update request", type: "error", text: "' . $e->getMessage() . '" , hide: true});';
            } catch (\Throwable $e) {
                $out .= 'createNotifyByObject({title: "Hotel update request", type: "error", text: "Server error" , hide: true});';
                Yii::error($e, 'HotelController:actionUpdateAjax');
            }

            return $out . '</script>';
        }

        return $this->renderAjax('update_ajax', [
            'model' => $form,
        ]);
    }

    /**
     * @return array
     */
    public function actionDeleteAjax(): array
    {
        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $model = $this->findModel($id);
            if (!$model->delete()) {
                throw new Exception('Product (' . $id . ') not deleted', 2);
            }

            if ((int) $model->pr_type_id === ProductType::PRODUCT_HOTEL && class_exists('\modules\hotel\HotelModule')) {
                $modelHotel = Hotel::findOne(['ph_product_id' => $model->pr_id]);
                if ($modelHotel) {
                    if (!$modelHotel->delete()) {
                        throw new Exception('Hotel (' . $modelHotel->ph_id . ') not deleted', 3);
                    }
                }
            }
        } catch (\Throwable $throwable) {
            return ['error' => 'Error: ' . $throwable->getMessage()];
        }

        return ['message' => 'Successfully removed product (' . $model->pr_id . ')'];
    }


    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param string $term
     * @param array $destType
     * @return Response
     */
    public function actionAjaxGetDestinationList(string $term = null, array $destType = []): Response
    {
        $keyCache = $term . implode('_', $destType);
        $result = Yii::$app->cache->get($keyCache);

        if ($result === false) {
            $apiHotelService = Yii::$app->getModule('hotel')->apiService;
            $result = $apiHotelService->searchDestination($term, '', '', '', $destType);
            if ($result) {
                Yii::$app->cache->set($keyCache, $result, 600);
            }
        }

        return $this->asJson(['results' => HotelFormatHelper::formatRows($result, $term)]);
    }

    public function actionAjaxCreateAlternativeProduct(): Response
    {
        $leadId = Yii::$app->request->post('leadId');
        $productQuoteId = Yii::$app->request->post('productQuoteId');

        if (!$leadId) {
            throw new BadRequestHttpException('Lead id not found in post params');
        }

        if (!$productQuoteId) {
            throw new BadRequestHttpException('Product quote id not found in post params');
        }

        $result = [
            'error' => false,
            'message' => ''
        ];

        try {
            $productQuote = $this->productQuoteRepository->find($productQuoteId);
            $lead = $this->leadRepository->find($leadId);

            $product = $this->productCloneService->clone($productQuote->pq_product_id, $lead->id, Auth::id());
            $this->productQuoteOriginService->create($product->pr_id, $productQuote->pq_id);
        } catch (\RuntimeException | NotFoundHttpException $e) {
            $result['message'] = $e->getMessage();
            $result['error'] = true;
        }

        return $this->asJson($result);
    }

    /**
     * Finds the Hotel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Hotel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Hotel
    {
        if (($model = Hotel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
