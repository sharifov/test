<?php

namespace frontend\controllers;

use modules\eventManager\src\entities\EventHandler;
use modules\eventManager\src\entities\search\EventHandlerSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * EventHandlerController implements the CRUD actions for EventHandler model.
 */
class EventHandlerController extends FController
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
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function init(): void
    {
        parent::init();
        $this->layoutCrud();
    }

    /**
     * Lists all EventHandler models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EventHandlerSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventHandler model.
     * @param int $eh_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($eh_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($eh_id),
        ]);
    }

    /**
     * Creates a new EventHandler model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new EventHandler();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'eh_id' => $model->eh_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EventHandler model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $eh_id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($eh_id)
    {
        $model = $this->findModel($eh_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'eh_id' => $model->eh_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EventHandler model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $eh_id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($eh_id)
    {
        $this->findModel($eh_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EventHandler model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $eh_id ID
     * @return EventHandler the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($eh_id)
    {
        if (($model = EventHandler::findOne(['eh_id' => $eh_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
