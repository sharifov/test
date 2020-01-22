<?php

namespace frontend\controllers;

use common\models\Lead;
use common\models\ProductType;
use frontend\models\form\ProductForm;
use modules\flight\models\Flight;
use modules\hotel\models\Hotel;
use sales\auth\Auth;
use Yii;
use common\models\Product;
use common\models\search\ProductSearch;
use frontend\controllers\FController;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends FController
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
                    'delete-ajax' => ['POST'],
                ],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
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
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->pr_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * @return array|string
     * @throws BadRequestHttpException
     */
    public function actionCreateAjax()
    {
        $model = new ProductForm(); //new Product();


        if ($model->load(Yii::$app->request->post())) {

            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->validate()) {
                $modelProduct = new Product();
                $modelProduct->attributes = $model->attributes;

                if ($modelProduct->save()) {

                    $errors = null;

                    if ((int) $model->pr_type_id === ProductType::PRODUCT_HOTEL) {
                        if (class_exists('\modules\hotel\HotelModule')) {
                            $modelHotel = new Hotel();
                            $modelHotel->ph_product_id = $modelProduct->pr_id;
                            if (!$modelHotel->save()) {
                                $errors = VarDumper::dumpAsString($modelHotel->errors);
                                Yii::error($errors,
                                    'ProductController:actionCreateAjax:Hotel:save');
                            }
                        } else {
                            Yii::error('Not exists class "\modules\hotel\HotelModule"',
                                'ProductController:actionCreateAjax:Hotel');
                        }
                    } elseif ((int) $model->pr_type_id === ProductType::PRODUCT_FLIGHT) {
                        if (class_exists('\modules\flight\FlightModule')) {
                            $modelFlight = new Flight();
                            $modelFlight->fl_product_id = $modelProduct->pr_id;
                            if (!$modelFlight->save()) {
                                $errors = VarDumper::dumpAsString($modelFlight->errors);
                                Yii::error($errors,
                                    'ProductController:actionCreateAjax:Flight:save');
                            }
                        } else {
                            Yii::error('Not exists class "\modules\flight\FlightModule"',
                                'ProductController:actionCreateAjax:Flight');
                        }
                    }

                    if ($errors) {
                        return ['errors' => $errors];
                    }
                    return ['message' => 'Successfully added a new product'];
                }

                return ['errors' => \yii\widgets\ActiveForm::validate($modelProduct)];
            }
            return ['errors' => \yii\widgets\ActiveForm::validate($model)];
        } else {

            $leadId = (int) Yii::$app->request->get('id');

            if (!$leadId) {
                throw new BadRequestHttpException('Not found lead identity.');
            }

            $lead = Lead::findOne($leadId);
            if (!$lead) {
                throw new BadRequestHttpException('Not found this lead');
            }

            $model->pr_lead_id = $leadId;
        }

        return $this->renderAjax('_ajax_form', [
            'model' => $model,
        ]);
    }


//    public function actionCreateAjax()
//    {
//        //$this->layout = false;
//        $model = new ProductForm(); //new Product();
//
//        if ($model->load(Yii::$app->request->post())) {
//
//            //\Yii::$app->response->format = Response::FORMAT_JSON;
//
//            if ($model->validate()) {
//                $modelProduct = new Product();
//                $modelProduct->attributes = $model->attributes;
//
//                if ($modelProduct->save()) {
//                    return ['message' => 'Successfully added a new product'];
//                }
//
//                //return ['errors' => \yii\widgets\ActiveForm::validate($modelProduct)];
//            }
//            //return ['errors' => \yii\widgets\ActiveForm::validate($model)];
//        }
//
//        return $this->renderAjax('_ajax_form', [
//            'model' => $model,
//        ]);
//    }


    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->pr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Product model.
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
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested product does not exist.', 1);
    }
}
