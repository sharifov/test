<?php

namespace frontend\controllers;

use Yii;
use sales\model\call\entity\callCommand\CallGatherSwitch;
use sales\model\call\entity\callCommand\search\CallGatherSwitchSearch;
use frontend\controllers\FController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CallGatherSwitchCrudController implements the CRUD actions for CallGatherSwitch model.
 */
class CallGatherSwitchCrudController extends FController
{
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
     * Lists all CallGatherSwitch models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CallGatherSwitchSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CallGatherSwitch model.
     * @param integer $cgs_ccom_id
     * @param integer $cgs_step
     * @param integer $cgs_case
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cgs_ccom_id, $cgs_step, $cgs_case)
    {
        return $this->render('view', [
            'model' => $this->findModel($cgs_ccom_id, $cgs_step, $cgs_case),
        ]);
    }

    /**
     * Creates a new CallGatherSwitch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CallGatherSwitch();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cgs_ccom_id' => $model->cgs_ccom_id, 'cgs_step' => $model->cgs_step, 'cgs_case' => $model->cgs_case]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CallGatherSwitch model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $cgs_ccom_id
     * @param integer $cgs_step
     * @param integer $cgs_case
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($cgs_ccom_id, $cgs_step, $cgs_case)
    {
        $model = $this->findModel($cgs_ccom_id, $cgs_step, $cgs_case);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cgs_ccom_id' => $model->cgs_ccom_id, 'cgs_step' => $model->cgs_step, 'cgs_case' => $model->cgs_case]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CallGatherSwitch model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $cgs_ccom_id
     * @param integer $cgs_step
     * @param integer $cgs_case
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($cgs_ccom_id, $cgs_step, $cgs_case)
    {
        $this->findModel($cgs_ccom_id, $cgs_step, $cgs_case)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CallGatherSwitch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $cgs_ccom_id
     * @param integer $cgs_step
     * @param integer $cgs_case
     * @return CallGatherSwitch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($cgs_ccom_id, $cgs_step, $cgs_case)
    {
        if (($model = CallGatherSwitch::findOne(['cgs_ccom_id' => $cgs_ccom_id, 'cgs_step' => $cgs_step, 'cgs_case' => $cgs_case])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
