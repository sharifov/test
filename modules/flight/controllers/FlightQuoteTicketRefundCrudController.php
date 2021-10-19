<?php

namespace modules\flight\controllers;

use modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund;
use modules\flight\src\entities\flightQuoteTicketRefund\search\FlightQuoteTicketRefundSearch;
use frontend\controllers\FController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FlightQuoteTicketRefundCrudController implements the CRUD actions for FlightQuoteTicketRefund model.
 */
class FlightQuoteTicketRefundCrudController extends FController
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
     * Lists all FlightQuoteTicketRefund models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FlightQuoteTicketRefundSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FlightQuoteTicketRefund model.
     * @param int $fqtr_id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($fqtr_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($fqtr_id),
        ]);
    }

    /**
     * Creates a new FlightQuoteTicketRefund model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FlightQuoteTicketRefund();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'fqtr_id' => $model->fqtr_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing FlightQuoteTicketRefund model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $fqtr_id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($fqtr_id)
    {
        $model = $this->findModel($fqtr_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fqtr_id' => $model->fqtr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FlightQuoteTicketRefund model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $fqtr_id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($fqtr_id)
    {
        $this->findModel($fqtr_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the FlightQuoteTicketRefund model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $fqtr_id ID
     * @return FlightQuoteTicketRefund the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($fqtr_id)
    {
        if (($model = FlightQuoteTicketRefund::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
