<?php

namespace frontend\controllers;

use common\models\Employee;
use sales\auth\Auth;
use Yii;
use sales\model\callLog\entity\callLogUserAccess\CallLogUserAccess;
use sales\model\callLog\entity\callLogUserAccess\search\CallLogUserAccessSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class CallLogUserAccessController extends FController
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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CallLogUserAccessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

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
        $model = new CallLogUserAccess();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->clua_access_start_dt && ($startDt = Employee::convertTimeFromUserDtToUTC(strtotime($model->clua_access_start_dt)))) {
                $model->clua_access_start_dt = $startDt;
            }
            if ($model->clua_access_finish_dt && ($finishDt = Employee::convertTimeFromUserDtToUTC(strtotime($model->clua_access_finish_dt)))) {
                $model->clua_access_finish_dt = $finishDt;
            }
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->clua_id]);
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
        if ($model->clua_access_start_dt && ($startDt = Employee::convertTimeFromUtcToUserTime(Auth::user()->timezone, strtotime($model->clua_access_start_dt)))) {
            $model->clua_access_start_dt = $startDt;
        }
        if ($model->clua_access_finish_dt && ($finishDt = Employee::convertTimeFromUtcToUserTime(Auth::user()->timezone, strtotime($model->clua_access_finish_dt)))) {
            $model->clua_access_finish_dt = $finishDt;
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->clua_access_start_dt && ($startDt = Employee::convertTimeFromUserDtToUTC(strtotime($model->clua_access_start_dt)))) {
                $model->clua_access_start_dt = $startDt;
            }
            if ($model->clua_access_finish_dt && ($finishDt = Employee::convertTimeFromUserDtToUTC(strtotime($model->clua_access_finish_dt)))) {
                $model->clua_access_finish_dt = $finishDt;
            }
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->clua_id]);
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
     * @return CallLogUserAccess
     * @throws NotFoundHttpException
     */
    protected function findModel($id): CallLogUserAccess
    {
        if (($model = CallLogUserAccess::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
