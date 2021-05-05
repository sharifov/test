<?php

namespace modules\flight\controllers;

use frontend\controllers\FController;
use Yii;
use modules\flight\models\FlightQuoteTicket;
use modules\flight\models\search\FlightQuoteTicketSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteTicketCrudController
 */
class FlightQuoteTicketCrudController extends FController
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
        $searchModel = new FlightQuoteTicketSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $fqt_pax_id
     * @param integer $fqt_fqb_id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($fqt_pax_id, $fqt_fqb_id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($fqt_pax_id, $fqt_fqb_id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new FlightQuoteTicket();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fqt_pax_id' => $model->fqt_pax_id, 'fqt_fqb_id' => $model->fqt_fqb_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fqt_pax_id
     * @param integer $fqt_fqb_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($fqt_pax_id, $fqt_fqb_id)
    {
        $model = $this->findModel($fqt_pax_id, $fqt_fqb_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'fqt_pax_id' => $model->fqt_pax_id, 'fqt_fqb_id' => $model->fqt_fqb_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $fqt_pax_id
     * @param integer $fqt_fqb_id
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($fqt_pax_id, $fqt_fqb_id): Response
    {
        $this->findModel($fqt_pax_id, $fqt_fqb_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param integer $fqt_pax_id
     * @param integer $fqt_fqb_id
     * @return FlightQuoteTicket
     * @throws NotFoundHttpException
     */
    protected function findModel($fqt_pax_id, $fqt_fqb_id): FlightQuoteTicket
    {
        if (($model = FlightQuoteTicket::findOne(['fqt_pax_id' => $fqt_pax_id, 'fqt_fqb_id' => $fqt_fqb_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('FlightQuoteTicket not found. (' . $fqt_pax_id . '/' . $fqt_fqb_id . ')');
    }
}
