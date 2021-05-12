<?php

namespace modules\fileStorage\controllers;

use frontend\controllers\FController;
use Yii;
use modules\fileStorage\src\entity\fileProductQuote\FileProductQuote;
use modules\fileStorage\src\entity\fileProductQuote\search\FileProductQuoteSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class FileProductQuoteController extends FController
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
        $searchModel = new FileProductQuoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $fpq_fs_id
     * @param integer $fpq_pq_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($fpq_fs_id, $fpq_pq_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($fpq_fs_id, $fpq_pq_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new FileProductQuote();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fpq_fs_id' => $model->fpq_fs_id, 'fpq_pq_id' => $model->fpq_pq_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fpq_fs_id
     * @param integer $fpq_pq_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($fpq_fs_id, $fpq_pq_id)
    {
        $model = $this->findModel($fpq_fs_id, $fpq_pq_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fpq_fs_id' => $model->fpq_fs_id, 'fpq_pq_id' => $model->fpq_pq_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fpq_fs_id
     * @param integer $fpq_pq_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($fpq_fs_id, $fpq_pq_id): Response
    {
        $this->findModel($fpq_fs_id, $fpq_pq_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $fpq_fs_id
     * @param integer $fpq_pq_id
     * @return FileProductQuote
     * @throws NotFoundHttpException
     */
    protected function findModel($fpq_fs_id, $fpq_pq_id): FileProductQuote
    {
        if (($model = FileProductQuote::findOne(['fpq_fs_id' => $fpq_fs_id, 'fpq_pq_id' => $fpq_pq_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
