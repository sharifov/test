<?php

namespace modules\fileStorage\controllers;

use frontend\controllers\FController;
use Yii;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileLead\search\FileLeadSearch;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;

class FileLeadController extends FController
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
        $searchModel = new FileLeadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $fld_fs_id
     * @param integer $fld_lead_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($fld_fs_id, $fld_lead_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($fld_fs_id, $fld_lead_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new FileLead();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fld_fs_id' => $model->fld_fs_id, 'fld_lead_id' => $model->fld_lead_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fld_fs_id
     * @param integer $fld_lead_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($fld_fs_id, $fld_lead_id)
    {
        $model = $this->findModel($fld_fs_id, $fld_lead_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fld_fs_id' => $model->fld_fs_id, 'fld_lead_id' => $model->fld_lead_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fld_fs_id
     * @param integer $fld_lead_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($fld_fs_id, $fld_lead_id): Response
    {
        $this->findModel($fld_fs_id, $fld_lead_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $fld_fs_id
     * @param integer $fld_lead_id
     * @return FileLead
     * @throws NotFoundHttpException
     */
    protected function findModel($fld_fs_id, $fld_lead_id): FileLead
    {
        if (($model = FileLead::findOne(['fld_fs_id' => $fld_fs_id, 'fld_lead_id' => $fld_lead_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
