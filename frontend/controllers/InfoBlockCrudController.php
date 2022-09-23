<?php

namespace frontend\controllers;

use common\models\InfoBlock;
use common\models\search\InfoBlockSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InfoBlockCrudController implements the CRUD actions for InfoBlock model.
 */
class InfoBlockCrudController extends FController
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all InfoBlock models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new InfoBlockSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InfoBlock model.
     * @param int $ib_id Ib ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($ib_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($ib_id),
        ]);
    }

    /**
     * Creates a new InfoBlock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new InfoBlock();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'ib_id' => $model->ib_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing InfoBlock model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $ib_id Ib ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($ib_id)
    {
        $model = $this->findModel($ib_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'ib_id' => $model->ib_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing InfoBlock model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $ib_id Ib ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($ib_id)
    {
        $this->findModel($ib_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the InfoBlock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $ib_id Ib ID
     * @return InfoBlock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ib_id)
    {
        if (($model = InfoBlock::findOne(['ib_id' => $ib_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Info Block not found by ID(' . $ib_id . ')');
    }
}
