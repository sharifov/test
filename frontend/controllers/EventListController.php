<?php

namespace frontend\controllers;

use modules\eventManager\src\entities\EventList;
use modules\eventManager\src\entities\search\EventListSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * EventListController implements the CRUD actions for EventList model.
 */
class EventListController extends FController
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
     * Lists all EventList models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EventListSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventList model.
     * @param int $el_id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($el_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($el_id),
        ]);
    }

    /**
     * Creates a new EventList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new EventList();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'el_id' => $model->el_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EventList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $el_id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($el_id)
    {
        $model = $this->findModel($el_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'el_id' => $model->el_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EventList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $el_id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($el_id)
    {
        $this->findModel($el_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EventList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $el_id ID
     * @return EventList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($el_id)
    {
        if (($model = EventList::findOne(['el_id' => $el_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
