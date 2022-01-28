<?php

namespace modules\featureFlag\controllers;

use frontend\controllers\FController;
use Yii;
use modules\featureFlag\src\entities\FeatureFlag;
use modules\featureFlag\src\entities\search\FeatureFlagSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class FeatureFlagController extends FController
{
    public const SCHEMA_VERSION = '0.1';
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-ajax' => ['POST'],
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
        $searchModel = new FeatureFlagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id ID
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
        $model = new FeatureFlag();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->invalidateCache();
            return $this->redirect(['view', 'id' => $model->ff_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->invalidateCache();
            return $this->redirect(['view', 'id' => $model->ff_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();
        $this->invalidateCache();
        return $this->redirect(['index']);
    }

    /**
     * @param int $id ID
     * @return FeatureFlag
     * @throws NotFoundHttpException
     */
    protected function findModel($id): FeatureFlag
    {
        if (($model = FeatureFlag::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return Response
     */
    public function actionClearCache(): Response
    {
        if ($this->invalidateCache()) {
            Yii::$app->session->setFlash('success', 'Feature Flag Cache was successfully cleared');
        } else {
            Yii::$app->session->setFlash('warning', 'Feature Flag Cache is disable');
        }

        return $this->redirect(['index']);
    }

    /**
     * @return bool
     */
    private function invalidateCache(): bool
    {
        return Yii::$app->ff->invalidateCache();
    }
}
