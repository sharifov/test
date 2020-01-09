<?php

namespace modules\flight\controllers;

use common\models\ProductType;
use modules\flight\models\forms\FlightForm;
use modules\hotel\models\Hotel;
use Yii;
use modules\flight\models\Flight;
use modules\flight\models\search\FlightSearch;
use frontend\controllers\FController;
use yii\base\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * FlightController implements the CRUD actions for Flight model.
 */
class FlightController extends FController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Flight models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FlightSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Flight model.
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
     * Creates a new Flight model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Flight();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->fl_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Flight model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->fl_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdateAjax()
    {

        $id = Yii::$app->request->get('id');

        try {
            $modelFlight = $this->findModel($id);
        } catch (\Throwable $throwable) {
            //Yii::$app->response->format = Response::FORMAT_JSON;
            //return ['error' => 'Error: ' . $throwable->getMessage()];
            return '<script>alert("'.$throwable->getMessage().'")</script>'; //['message' => 'Successfully updated Hotel request'];
        }

        $model = new FlightForm();
        $model->fl_id = $modelFlight->fl_id;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {

                //$modelHotel->attributes = $model->attributes;

                // $modelFlight->ph_zone_code = $model->ph_zone_code;


                //VarDumper::dump($modelHotel->attributes); exit;
                if ($modelFlight->save()) {
                    return '<script>$("#modal-sm").modal("hide"); $.pjax.reload({container: "#pjax-product-search-' . $modelFlight->fl_product_id . '"});</script>';
                }

                Yii::error($modelFlight->errors, 'Module:FlightController:actionUpdateAjax:Flight:save');


                //  return ['errors' => \yii\widgets\ActiveForm::validate($modelProduct)];
            }
            //return ['errors' => $model->errors];
        } else {
            $model->attributes = $modelFlight->attributes;
        }

        return $this->renderAjax('update_ajax', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Flight model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
     * Finds the Flight model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Flight the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Flight::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
