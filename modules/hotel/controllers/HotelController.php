<?php

namespace modules\hotel\controllers;

use modules\product\src\entities\productType\ProductType;
use modules\hotel\models\forms\HotelForm;
use modules\hotel\src\helpers\HotelFormatHelper;
use Yii;
use modules\hotel\models\Hotel;
use modules\hotel\models\search\HotelSearch;
use frontend\controllers\FController;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * HotelController implements the CRUD actions for Hotel model.
 */
class HotelController extends FController
{
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
            $modelHotel = $this->findModel($id);
        } catch (\Throwable $throwable) {
            //Yii::$app->response->format = Response::FORMAT_JSON;
            //return ['error' => 'Error: ' . $throwable->getMessage()];
            return '<script>alert("'.$throwable->getMessage().'")</script>'; //['message' => 'Successfully updated Hotel request'];
        }

        $model = new HotelForm();
        $model->ph_id = $modelHotel->ph_id;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {

                //$modelHotel->attributes = $model->attributes;

                $modelHotel->ph_zone_code = $model->ph_zone_code;
                $modelHotel->ph_hotel_code = $model->ph_hotel_code;
                $modelHotel->ph_destination_code = $model->ph_destination_code;
                $modelHotel->ph_destination_label = $model->ph_destination_label;
                $modelHotel->ph_check_in_date = $model->ph_check_in_date;
                $modelHotel->ph_check_out_date = $model->ph_check_out_date;

                $modelHotel->ph_max_price_rate = $model->ph_max_price_rate;
                $modelHotel->ph_min_price_rate = $model->ph_min_price_rate;

                $modelHotel->ph_max_star_rate = $model->ph_max_star_rate;
                $modelHotel->ph_min_star_rate = $model->ph_min_star_rate;

                //VarDumper::dump($modelHotel->attributes); exit;
                if ($modelHotel->save()) {
                    return '<script>$("#modal-sm").modal("hide"); $.pjax.reload({container: "#pjax-product-search-' . $modelHotel->ph_product_id . '"});</script>';
                }

                Yii::error($modelHotel->errors, 'Module:HotelController:actionUpdateAjax:Hotel:save');


              //  return ['errors' => \yii\widgets\ActiveForm::validate($modelProduct)];
            }
            //return ['errors' => $model->errors];
        } else {
            $model->attributes = $modelHotel->attributes;
        }

        return $this->renderAjax('update_ajax', [
            'model' => $model,
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
                throw new Exception('Product ('.$id.') not deleted', 2);
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

		if($result === false) {
			$apiHotelService = Yii::$app->getModule('hotel')->apiService;
			$result = $apiHotelService->searchDestination($term, '', '', '', $destType);
			if($result) {
				Yii::$app->cache->set($keyCache, $result, 600);
			}
		}

		return $this->asJson(['results' => HotelFormatHelper::formatRows($result, $term)]);
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
