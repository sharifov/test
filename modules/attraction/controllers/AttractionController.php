<?php

namespace modules\attraction\controllers;

use modules\attraction\models\Attraction;
use modules\attraction\src\useCases\request\update\AttractionUpdateRequestForm;
use modules\attraction\src\useCases\request\update\AttractionRequestUpdateService;
use modules\product\src\entities\productType\ProductType;
use modules\hotel\src\helpers\HotelFormatHelper;
use Yii;
use modules\hotel\models\Hotel;
use modules\attraction\models\search\AttractionSearch;
use frontend\controllers\FController;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * HotelController implements the CRUD actions for Hotel model.
 *
 * @property AttractionRequestUpdateService $attractionRequestUpdateService
 */
class AttractionController extends FController
{
    private $attractionRequestUpdateService;

    public function __construct($id, $module, AttractionRequestUpdateService $attractionRequestUpdateService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->attractionRequestUpdateService = $attractionRequestUpdateService;
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
        $searchModel = new AttractionSearch();
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
        $model = new Attraction();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->atn_id]);
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
            return $this->redirect(['view', 'id' => $model->atn_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdateAjax()
    {
        $id = Yii::$app->request->get('id');

        try {
            $attraction = $this->findModel($id);
        } catch (\Throwable $throwable) {
            return '<script>alert("' . $throwable->getMessage() . '")</script>';
        }

        $form = new AttractionUpdateRequestForm($attraction);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $out = '<script>$("#modal-sm").modal("hide"); pjaxReload({container: "#pjax-product-search-' . $attraction->atn_product_id . '"});';
            try {
                $this->attractionRequestUpdateService->update($form);
                $out .= 'createNotifyByObject({title: "Attraction update request", type: "success", text: "Success" , hide: true});';
            } catch (\DomainException $e) {
                $out .= 'createNotifyByObject({title: "Attraction update request", type: "error", text: "' . $e->getMessage() . '" , hide: true});';
            } catch (\Throwable $e) {
                $out .= 'createNotifyByObject({title: "Attraction update request", type: "error", text: "Server error" , hide: true});';
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
            $apiAttractionService = Yii::$app->getModule('attraction')->apiService;
            $result = $apiAttractionService->searchDestination($term, '', '', '', $destType);
            if ($result) {
                Yii::$app->cache->set($keyCache, $result, 600);
            }
        }

        return $this->asJson(['results' => HotelFormatHelper::formatRows($result, $term)]);
    }

    /**
     * Finds the Hotel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Attraction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Attraction
    {
        if (($model = Attraction::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
