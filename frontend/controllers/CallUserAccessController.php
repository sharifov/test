<?php

namespace frontend\controllers;

use Yii;
use common\models\CallUserAccess;
use common\models\search\CallUserAccessSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CallUserAccessController implements the CRUD actions for CallUserAccess model.
 */
class CallUserAccessController extends FController
{

    public function behaviors()
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
     * Lists all CallUserAccess models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CallUserAccessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CallUserAccess model.
     * @param integer $cua_call_id
     * @param integer $cua_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cua_call_id, $cua_user_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($cua_call_id, $cua_user_id),
        ]);
    }

    /**
     * Creates a new CallUserAccess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CallUserAccess();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cua_call_id' => $model->cua_call_id, 'cua_user_id' => $model->cua_user_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CallUserAccess model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $cua_call_id
     * @param integer $cua_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($cua_call_id, $cua_user_id)
    {
        $model = $this->findModel($cua_call_id, $cua_user_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cua_call_id' => $model->cua_call_id, 'cua_user_id' => $model->cua_user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CallUserAccess model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $cua_call_id
     * @param integer $cua_user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($cua_call_id, $cua_user_id)
    {
        $this->findModel($cua_call_id, $cua_user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CallUserAccess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $cua_call_id
     * @param integer $cua_user_id
     * @return CallUserAccess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($cua_call_id, $cua_user_id)
    {
        if (($model = CallUserAccess::findOne(['cua_call_id' => $cua_call_id, 'cua_user_id' => $cua_user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
