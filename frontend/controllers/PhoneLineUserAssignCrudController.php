<?php

namespace frontend\controllers;

use sales\auth\Auth;
use Yii;
use sales\model\phoneLine\phoneLineUserAssign\entity\PhoneLineUserAssign;
use sales\model\phoneLine\phoneLineUserAssign\entity\search\PhoneLineUserAssignSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class PhoneLineUserAssignCrudController extends FController
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

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new PhoneLineUserAssignSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Auth::user());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $plus_line_id
     * @param integer $plus_user_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($plus_line_id, $plus_user_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($plus_line_id, $plus_user_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new PhoneLineUserAssign();

        $model->plus_created_user_id = Auth::id();
        $model->plus_updated_user_id = Auth::id();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'plus_line_id' => $model->plus_line_id, 'plus_user_id' => $model->plus_user_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $plus_line_id
     * @param integer $plus_user_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($plus_line_id, $plus_user_id)
    {
        $model = $this->findModel($plus_line_id, $plus_user_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'plus_line_id' => $model->plus_line_id, 'plus_user_id' => $model->plus_user_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $plus_line_id
     * @param integer $plus_user_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($plus_line_id, $plus_user_id): Response
    {
        $this->findModel($plus_line_id, $plus_user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $plus_line_id
     * @param integer $plus_user_id
     * @return PhoneLineUserAssign
     * @throws NotFoundHttpException
     */
    protected function findModel($plus_line_id, $plus_user_id): PhoneLineUserAssign
    {
        if (($model = PhoneLineUserAssign::findOne(['plus_line_id' => $plus_line_id, 'plus_user_id' => $plus_user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
