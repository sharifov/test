<?php

namespace frontend\controllers;

use Yii;
use sales\model\shiftSchedule\entity\userShiftAssign\UserShiftAssign;
use sales\model\shiftSchedule\entity\userShiftAssign\search\SearchUserShiftAssign;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class UserShiftAssignCrudController extends FController
{
    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

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
        $searchModel = new SearchUserShiftAssign();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $usa_user_id
     * @param integer $usa_ssr_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($usa_user_id, $usa_ssr_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($usa_user_id, $usa_ssr_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new UserShiftAssign();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'usa_user_id' => $model->usa_user_id, 'usa_ssr_id' => $model->usa_ssr_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $usa_user_id
     * @param integer $usa_ssr_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($usa_user_id, $usa_ssr_id)
    {
        $model = $this->findModel($usa_user_id, $usa_ssr_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'usa_user_id' => $model->usa_user_id, 'usa_ssr_id' => $model->usa_ssr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $usa_user_id
     * @param integer $usa_ssr_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($usa_user_id, $usa_ssr_id): Response
    {
        $this->findModel($usa_user_id, $usa_ssr_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $usa_user_id
     * @param integer $usa_ssr_id
     * @return UserShiftAssign
     * @throws NotFoundHttpException
     */
    protected function findModel($usa_user_id, $usa_ssr_id): UserShiftAssign
    {
        if (($model = UserShiftAssign::findOne(['usa_user_id' => $usa_user_id, 'usa_ssr_id' => $usa_ssr_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
