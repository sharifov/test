<?php

namespace modules\flight\controllers;

use frontend\controllers\FController;
use Yii;
use modules\flight\models\FlightRequest;
use modules\flight\models\search\FlightRequestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

class FlightRequestCrudController extends FController
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
        $searchModel = new FlightRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $fr_id
     * @param integer $fr_year
     * @param integer $fr_month
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($fr_id, $fr_year, $fr_month): string
    {
        return $this->render('view', [
            'model' => $this->findModel($fr_id, $fr_year, $fr_month),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new FlightRequest();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fr_id' => $model->fr_id, 'fr_year' => $model->fr_year, 'fr_month' => $model->fr_month]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fr_id
     * @param integer $fr_year
     * @param integer $fr_month
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($fr_id, $fr_year, $fr_month)
    {
        $model = $this->findModel($fr_id, $fr_year, $fr_month);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fr_id' => $model->fr_id, 'fr_year' => $model->fr_year, 'fr_month' => $model->fr_month]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fr_id
     * @param integer $fr_year
     * @param integer $fr_month
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($fr_id, $fr_year, $fr_month): Response
    {
        $this->findModel($fr_id, $fr_year, $fr_month)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $fr_id
     * @param integer $fr_year
     * @param integer $fr_month
     * @return FlightRequest
     * @throws NotFoundHttpException
     */
    protected function findModel($fr_id, $fr_year, $fr_month): FlightRequest
    {
        if (($model = FlightRequest::findOne(['fr_id' => $fr_id, 'fr_year' => $fr_year, 'fr_month' => $fr_month])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('FlightRequest does not exist.');
    }
}
