<?php

namespace modules\fileStorage\controllers;

use frontend\controllers\FController;
use Yii;
use modules\fileStorage\src\entity\fileCase\FileCase;
use modules\fileStorage\src\entity\fileCase\search\FileCaseSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class FileCaseController extends FController
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
        $searchModel = new FileCaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $fc_fs_id
     * @param integer $fc_case_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($fc_fs_id, $fc_case_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($fc_fs_id, $fc_case_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new FileCase();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fc_fs_id' => $model->fc_fs_id, 'fc_case_id' => $model->fc_case_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fc_fs_id
     * @param integer $fc_case_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($fc_fs_id, $fc_case_id)
    {
        $model = $this->findModel($fc_fs_id, $fc_case_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fc_fs_id' => $model->fc_fs_id, 'fc_case_id' => $model->fc_case_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fc_fs_id
     * @param integer $fc_case_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($fc_fs_id, $fc_case_id): Response
    {
        $this->findModel($fc_fs_id, $fc_case_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $fc_fs_id
     * @param integer $fc_case_id
     * @return FileCase
     * @throws NotFoundHttpException
     */
    protected function findModel($fc_fs_id, $fc_case_id): FileCase
    {
        if (($model = FileCase::findOne(['fc_fs_id' => $fc_fs_id, 'fc_case_id' => $fc_case_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
