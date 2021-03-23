<?php

namespace modules\attraction\controllers;

use modules\attraction\models\Attraction;
use modules\attraction\models\forms\PaxForm;
use modules\attraction\models\forms\AttractionPaxForm;
use Yii;
use modules\attraction\models\AttractionPax;
use modules\attraction\models\search\AttractionPaxSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

/**
 * AttractionPaxController implements the CRUD actions for AttractionPax model.
 */
class AttractionPaxController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCreateAjax()
    {
        $model = new PaxForm();

        if ($model->load(Yii::$app->request->post())) {
            //Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->validate()) {
                if ($model->atn_pax_list) {
                    $paxIDs = [];
                    foreach ($model->atn_pax_list as $paxData) {
                        $paxForm = new AttractionPaxForm();
                        $paxForm->attributes = $paxData;
                        $paxForm->atnp_atn_id = $model->atn_attraction_id;

                        $paxModel = null;
                        if ($paxForm->atnp_id) {
                            $paxModel = AttractionPax::findOne($paxForm->atnp_id);
                        }

                        if (!$paxModel) {
                            $paxModel = new AttractionPax();
                            $paxModel->atnp_atn_id = $model->atn_attraction_id;
                        }

                        $paxModel->attributes = $paxForm->attributes;
                        if (!$paxModel->save()) {
                            Yii::error('attr: '  . VarDumper::dumpAsString($paxModel->attributes) . ', errors:' . VarDumper::dumpAsString($paxModel->errors), 'AttractionPaxController:actionCreateAjax:AttractionPaxPax:save');
                        } else {
                            $paxIDs[] = $paxModel->atnp_id;
                        }
                    }

                    $paxForDelete = AttractionPax::find()->where(['NOT IN', 'atnp_id', $paxIDs])->andWhere(['atnp_atn_id' => $model->atn_attraction_id])->all();
                    if ($paxForDelete) {
                        foreach ($paxForDelete as $paxDeleteItem) {
                            $paxDeleteItem->delete();
                        }
                    }
                }

                /*$hotel = $modelRoom->hrHotel;
                $this->hotelQuoteSearchService->clearCache($hotel->ph_request_hash_key);*/
                return '<script>$("#modal-df").modal("hide"); pjaxReload({container: "#pjax-product-search-' . $model->product_id . '"});</script>';
                //return '<script>$("#modal-df").modal("hide"); pjaxReload({container: "#pjax-hotel-rooms-' . $modelRoom->atn_attraction_id . '"});</script>';
            }
            //return ['errors' => \yii\widgets\ActiveForm::validate($model)];
        } else {
            $attractionId = (int) Yii::$app->request->get('id');

            if (!$attractionId) {
                throw new BadRequestHttpException('Not found Attraction identity.');
            }

            $attraction = Attraction::findOne($attractionId);
            if (!$attraction) {
                throw new BadRequestHttpException('Not found this Attraction');
            }

            $model->atn_attraction_id = $attractionId;
            $model->product_id = $attraction->atn_product_id;
        }

        return $this->renderAjax('create_ajax_form', [
            'model' => $model,
        ]);
    }

    public function actionUpdateAjax()
    {
        //$model = new PaxForm();
        $attractionId = (int) Yii::$app->request->get('id');

        try {
            $modelAttraction = Attraction::findOne($attractionId);
        } catch (\Throwable $throwable) {
            return $throwable->getMessage();
        }

        $model = new PaxForm();
        $model->product_id = $modelAttraction->atn_product_id;
        $model->atn_attraction_id = $modelAttraction->atn_id;

        if ($model->load(Yii::$app->request->post())) {
            //Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->validate()) {
                if ($model->atn_pax_list) {
                    $paxIDs = [];
                    foreach ($model->atn_pax_list as $paxData) {
                        $paxForm = new AttractionPaxForm();
                        $paxForm->attributes = $paxData;
                        $paxForm->atnp_atn_id = $model->atn_attraction_id;

                        $paxModel = null;
                        if ($paxForm->atnp_id) {
                            $paxModel = AttractionPax::findOne($paxForm->atnp_id);
                        }

                        if (!$paxModel) {
                            $paxModel = new AttractionPax();
                            $paxModel->atnp_atn_id = $model->atn_attraction_id;
                        }

                        $paxModel->attributes = $paxForm->attributes;
                        if (!$paxModel->save()) {
                            Yii::error('attr: '  . VarDumper::dumpAsString($paxModel->attributes) . ', errors:' . VarDumper::dumpAsString($paxModel->errors), 'AttractionPaxController:actionCreateAjax:AttractionPaxPax:save');
                        } else {
                            $paxIDs[] = $paxModel->atnp_id;
                        }
                    }

                    $paxForDelete = AttractionPax::find()->where(['NOT IN', 'atnp_id', $paxIDs])->andWhere(['atnp_atn_id' => $model->atn_attraction_id])->all();
                    if ($paxForDelete) {
                        foreach ($paxForDelete as $paxDeleteItem) {
                            $paxDeleteItem->delete();
                        }
                    }
                }

                /*$hotel = $modelRoom->hrHotel;
                $this->hotelQuoteSearchService->clearCache($hotel->ph_request_hash_key);*/
                return '<script>$("#modal-df").modal("hide"); pjaxReload({container: "#pjax-product-search-' . $model->product_id . '"});</script>';
                //return '<script>$("#modal-df").modal("hide"); pjaxReload({container: "#pjax-hotel-rooms-' . $modelRoom->atn_attraction_id . '"});</script>';
            }
            //return ['errors' => \yii\widgets\ActiveForm::validate($model)];
        } else {
            $model->attributes = $modelAttraction->attributes;

            $paxData = [];
            if ($modelAttraction->attractionPaxes) {
                foreach ($modelAttraction->attractionPaxes as $paxItem) {
                    $paxData[] = ArrayHelper::toArray($paxItem);
                }
            }

            $model->atn_pax_list = $paxData;
        }

        return $this->renderAjax('update_ajax_form', [
            'model' => $model,
        ]);
    }

    /**
     * Lists all AttractionPax models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AttractionPaxSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AttractionPax model.
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
     * Creates a new AttractionPax model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AttractionPax();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->atnp_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AttractionPax model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->atnp_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AttractionPax model.
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
     * Finds the AttractionPax model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AttractionPax the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AttractionPax::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
