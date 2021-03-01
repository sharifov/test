<?php

namespace modules\fileStorage\controllers;

use frontend\controllers\FController;
use Yii;
use modules\fileStorage\src\entity\fileOrder\FileOrder;
use modules\fileStorage\src\entity\fileOrder\search\FileOrderSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class FileOrderController extends FController
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
        $searchModel = new FileOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $fo_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($fo_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($fo_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new FileOrder();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fo_id' => $model->fo_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $fo_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($fo_id)
    {
        $model = $this->findModel($fo_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fo_id' => $model->fo_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $fo_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($fo_id): Response
    {
        $this->findModel($fo_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param int $fo_id
     * @return FileOrder
     * @throws NotFoundHttpException
     */
    protected function findModel($fo_id): FileOrder
    {
        if (($model = FileOrder::findOne(['fo_id' => $fo_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('FileOrder not found');
    }
}
