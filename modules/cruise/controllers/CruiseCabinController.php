<?php

namespace modules\cruise\controllers;

use modules\cruise\src\entity\cruise\Cruise;
use modules\cruise\src\entity\cruiseCabinPax\CruiseCabinPax;
use modules\cruise\src\useCase\createCabin\CreateCabinForm;
use modules\cruise\src\useCase\createCabin\CruiseCabinPaxForm;
use Yii;
use modules\cruise\src\entity\cruiseCabin\CruiseCabin;
use modules\cruise\src\entity\cruiseCabin\search\CruiseCabinSearch;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class CruiseCabinController extends Controller
{
    /**
    * @return array
    */
    public function behaviors(): array
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

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CruiseCabinSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new CruiseCabin();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->crc_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->crc_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
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
     * @param integer $id
     * @return CruiseCabin
     * @throws NotFoundHttpException
     */
    protected function findModel($id): CruiseCabin
    {
        if (($model = CruiseCabin::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionCreateAjax()
    {
        $model = new CreateCabinForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $modelCabin = new CruiseCabin();
                $modelCabin->attributes = $model->attributes;

                if ($modelCabin->save()) {
                    if ($model->crc_pax_list) {
                        $paxIDs = [];
                        foreach ($model->crc_pax_list as $paxData) {
                            $paxForm = new CruiseCabinPaxForm();
                            $paxForm->attributes = $paxData;
                            $paxForm->crp_cruise_cabin_id = $modelCabin->crc_id;

                            $paxModel = null;
                            if ($paxForm->crp_id) {
                                $paxModel = CruiseCabinPax::findOne($paxForm->crp_id);
                            }

                            if (!$paxModel) {
                                $paxModel = new CruiseCabinPax();
                            }

                            $paxModel->attributes = $paxForm->attributes;
                            if (!$paxModel->save()) {
                                Yii::error('attr: '  . VarDumper::dumpAsString($paxModel->attributes) . ', errors:' . VarDumper::dumpAsString($paxModel->errors), 'CruiseCabinController:actionCreateAjax:CruiseCabinPax:save');
                            } else {
                                $paxIDs[] = $paxModel->crp_id;
                            }
                        }

                        $paxForDelete = CruiseCabinPax::find()->where(['NOT IN', 'crp_id', $paxIDs])->andWhere(['crp_cruise_cabin_id' => $modelCabin->crc_id])->all();
                        if ($paxForDelete) {
                            foreach ($paxForDelete as $paxDeleteItem) {
                                $paxDeleteItem->delete();
                            }
                        }
                    }

                    return '<script>$("#modal-df").modal("hide"); pjaxReload({container: "#pjax-product-search-' . $modelCabin->cruise->crs_product_id . '"});</script>';
                }
            }
            //return ['errors' => \yii\widgets\ActiveForm::validate($model)];
        } else {
            $cruiseId = (int) Yii::$app->request->get('id');

            if (!$cruiseId) {
                throw new BadRequestHttpException('Not found Cruise identity.');
            }

            $cruise = Cruise::findOne($cruiseId);
            if (!$cruise) {
                throw new BadRequestHttpException('Not found this Cruise');
            }

            $model->crc_cruise_id = $cruiseId;
        }

        return $this->renderAjax('create_ajax_form', [
            'model' => $model,
        ]);
    }
}
