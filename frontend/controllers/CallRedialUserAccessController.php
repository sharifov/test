<?php

namespace frontend\controllers;

use src\auth\Auth;
use Yii;
use src\model\leadRedial\entity\CallRedialUserAccess;
use src\model\leadRedial\entity\search\CallRedialUserAccessSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class CallRedialUserAccessController extends FController
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
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CallRedialUserAccessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $crua_lead_id Lead ID
     * @param int $crua_user_id Employee ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($crua_lead_id, $crua_user_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($crua_lead_id, $crua_user_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new CallRedialUserAccess();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'crua_user_id' => $model->crua_user_id, 'crua_lead_id' => $model->crua_lead_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $crua_lead_id Lead ID
     * @param int $crua_user_id Employee ID
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($crua_lead_id, $crua_user_id)
    {
        $model = $this->findModel($crua_lead_id, $crua_user_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'crua_user_id' => $model->crua_user_id, 'crua_lead_id' => $model->crua_lead_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $crua_lead_id Lead ID
     * @param int $crua_user_id Employee ID
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($crua_lead_id, $crua_user_id): Response
    {
        $this->findModel($crua_lead_id, $crua_user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $crua_lead_id Lead ID
     * @param int $crua_user_id Employee ID
     * @return CallRedialUserAccess
     * @throws NotFoundHttpException
     */
    protected function findModel($crua_lead_id, $crua_user_id): CallRedialUserAccess
    {
        if (($model = CallRedialUserAccess::find()->andWhere(['crua_user_id' => $crua_user_id, 'crua_lead_id' => $crua_lead_id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
